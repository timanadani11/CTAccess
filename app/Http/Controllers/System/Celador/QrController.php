<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Portatiles;
use App\Models\Vehiculo;
use App\Models\Acceso;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class QrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    public function index()
    {
        $estadisticas = Acceso::estadisticasDelDia();
        $accesosActivos = Acceso::activos()
            ->with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada')
            ->take(10)
            ->get();
        
        $historialReciente = Acceso::delDia()
            ->with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada')
            ->take(10)
            ->get();

        return Inertia::render('System/Celador/Qr/Index', [
            'estadisticas' => $estadisticas,
            'accesosActivos' => $accesosActivos,
            'historialReciente' => $historialReciente,
        ]);
    }

    public function registrarAcceso(Request $request)
    {
        $request->validate([
            'qr_persona' => 'required|string',
            'qr_portatil' => 'nullable|string',
            'qr_vehiculo' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $usuario = auth('system')->user();
                
                // 1. Buscar persona por QR
                $persona = $this->buscarPersonaPorQr($request->qr_persona);
                
                // 2. Verificar si tiene acceso activo (determina si es entrada o salida)
                $accesoActivo = $persona->getAccesoActivo();
                
                if (!$accesoActivo) {
                    // Es una ENTRADA
                    return $this->procesarEntrada($persona, $request, $usuario);
                } else {
                    // Es una SALIDA
                    return $this->procesarSalida($persona, $accesoActivo, $request, $usuario);
                }
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al registrar acceso QR', [
                'error' => $e->getMessage(),
                'qr_persona' => $request->qr_persona,
                'usuario_id' => auth('system')->id()
            ]);
            
            throw ValidationException::withMessages([
                'qr_persona' => 'Error interno del sistema. Contacte al administrador.'
            ]);
        }
    }

    private function buscarPersonaPorQr($qrPersona)
    {
        // Extraer documento del QR (formato: PERSONA_123456789)
        $documento = str_replace('PERSONA_', '', $qrPersona);
        
        $persona = Persona::buscarPorDocumento($documento);
        
        if (!$persona) {
            throw ValidationException::withMessages([
                'qr_persona' => 'Persona no encontrada con el documento: ' . $documento
            ]);
        }

        return $persona;
    }

    private function procesarEntrada($persona, $request, $usuario)
    {
        // 🔥 OBTENER AUTOMÁTICAMENTE portátil y vehículo asociados a la persona
        $persona->load(['portatiles', 'vehiculos']);
        
        $portatilId = null;
        $vehiculoId = null;

        // Si la persona tiene portátil registrado, usarlo automáticamente
        if ($persona->portatiles->isNotEmpty()) {
            $portatil = $persona->portatiles->first(); // Portátil principal
            $portatilId = $portatil->portatil_id;
        }

        // Si la persona tiene vehículo registrado, usarlo automáticamente
        if ($persona->vehiculos->isNotEmpty()) {
            $vehiculo = $persona->vehiculos->first(); // Vehículo principal
            $vehiculoId = $vehiculo->id;
        }

        // Registrar entrada con los datos obtenidos automáticamente
        $acceso = Acceso::registrarEntrada(
            $persona->idPersona,
            $portatilId,
            $vehiculoId,
            $usuario->idUsuario
        );

        return back()->with('success', [
            'tipo' => 'entrada',
            'mensaje' => 'Entrada registrada exitosamente',
            'persona' => $persona->Nombre,
            'documento' => $persona->documento,
            'hora' => $acceso->fecha_entrada->format('H:i:s'),
            'portatil' => $portatilId ? 'Sí' : 'No',
            'vehiculo' => $vehiculoId ? 'Sí' : 'No',
            'portatil_info' => $portatilId ? $persona->portatiles->first()->marca . ' ' . $persona->portatiles->first()->modelo : null,
            'vehiculo_info' => $vehiculoId ? $persona->vehiculos->first()->tipo . ' - ' . $persona->vehiculos->first()->placa : null
        ]);
    }

    private function procesarSalida($persona, $accesoActivo, $request, $usuario)
    {
        $errores = [];
        $requiereVerificacion = false;
        $confio = $request->input('confiar', false); // Nuevo: el celador confió

        // 🔥 VERIFICACIÓN DE PORTÁTIL EN SALIDA
        if ($accesoActivo->portatil_id) {
            $requiereVerificacion = true;
            
            if ($confio) {
                // ✅ El celador confió - NO verificar
                Log::info('Salida sin verificación de portátil (celador confió)', [
                    'persona_id' => $persona->idPersona,
                    'acceso_id' => $accesoActivo->idAcceso,
                    'portatil_entrada' => $accesoActivo->portatil->serial
                ]);
            } elseif ($request->has('serial_verificado')) {
                // 🔍 Se verificó el serial
                $serialVerificado = $request->serial_verificado;
                $serialEsperado = $accesoActivo->portatil->serial;
                
                if ($serialVerificado != $serialEsperado) {
                    // ⚠️ Serial NO COINCIDE - INCIDENCIA
                    $errores[] = "Portátil NO coincide. Entrada: {$serialEsperado}, Verificado: {$serialVerificado}";
                }
            } elseif (!$request->qr_portatil) {
                // ⚠️ No escaneó QR de portátil - INCIDENCIA
                $errores[] = 'No se verificó el portátil (Serial esperado: ' . $accesoActivo->portatil->serial . ')';
            } else {
                // Verificación tradicional con QR completo
                $portatil = $this->buscarPortatilPorQr($request->qr_portatil);
                
                if ($portatil->portatil_id != $accesoActivo->portatil_id) {
                    $errores[] = 'Portátil NO coincide. Entrada: ' . $accesoActivo->portatil->serial . ', Verificado: ' . $portatil->serial;
                }
            }
        }

        // 🔥 VERIFICACIÓN DE VEHÍCULO EN SALIDA
        if ($accesoActivo->vehiculo_id) {
            $requiereVerificacion = true;
            
            if ($confio) {
                // ✅ El celador confió - NO verificar
                Log::info('Salida sin verificación de vehículo (celador confió)', [
                    'persona_id' => $persona->idPersona,
                    'acceso_id' => $accesoActivo->idAcceso,
                    'vehiculo_entrada' => $accesoActivo->vehiculo->placa
                ]);
            } elseif ($request->has('placa_verificada')) {
                // 🔍 Se verificó la placa
                $placaVerificada = $request->placa_verificada;
                $placaEsperada = $accesoActivo->vehiculo->placa;
                
                if ($placaVerificada != $placaEsperada) {
                    // ⚠️ Placa NO COINCIDE - INCIDENCIA
                    $errores[] = "Vehículo NO coincide. Entrada: {$placaEsperada}, Verificado: {$placaVerificada}";
                }
            } elseif (!$request->qr_vehiculo) {
                // ⚠️ No escaneó QR de vehículo - INCIDENCIA
                $errores[] = 'No se verificó el vehículo (Placa esperada: ' . $accesoActivo->vehiculo->placa . ')';
            } else {
                // Verificación tradicional con QR completo
                $vehiculo = $this->buscarVehiculoPorQr($request->qr_vehiculo);
                
                if ($vehiculo->id != $accesoActivo->vehiculo_id) {
                    $errores[] = 'Vehículo NO coincide. Entrada: ' . $accesoActivo->vehiculo->placa . ', Verificado: ' . $vehiculo->placa;
                }
            }
        }

        // Si hay errores, registrar incidencia PERO PERMITIR SALIDA
        if (!empty($errores)) {
            $descripcion = 'Inconsistencias en salida: ' . implode(' | ', $errores);
            $incidencia = $accesoActivo->marcarIncidencia($descripcion, $usuario->idUsuario);
            
            // Marcar salida CON incidencia
            $accesoActivo->marcarSalida($usuario->idUsuario);
            
            return back()->with('warning', [
                'tipo' => 'salida_con_incidencia',
                'mensaje' => '⚠️ SALIDA REGISTRADA CON INCIDENCIA',
                'persona' => $persona->Nombre,
                'documento' => $persona->documento,
                'errores' => $errores,
                'incidencia_id' => $incidencia->incidenciaId,
                'acceso_id' => $accesoActivo->idAcceso,
                'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i:s'),
                'hora_salida' => $accesoActivo->fecha_salida->format('H:i:s')
            ]);
        }

        // ✅ Registrar salida exitosa (sin errores o con confianza)
        $accesoActivo->marcarSalida($usuario->idUsuario);

        $mensaje = $confio 
            ? '✅ Salida registrada (celador confió en verificación)'
            : '✅ Salida registrada exitosamente (equipos verificados)';

        return back()->with('success', [
            'tipo' => 'salida',
            'mensaje' => $mensaje,
            'persona' => $persona->Nombre,
            'documento' => $persona->documento,
            'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i:s'),
            'hora_salida' => $accesoActivo->fecha_salida->format('H:i:s'),
            'duracion' => $accesoActivo->duracion,
            'verificaciones_ok' => $requiereVerificacion && !$confio,
            'confio' => $confio
        ]);
    }

    private function buscarPortatilPorQr($qrPortatil)
    {
        $serial = str_replace('PORTATIL_', '', $qrPortatil);
        $portatil = Portatiles::where('serial', $serial)->first();
        
        if (!$portatil) {
            throw ValidationException::withMessages([
                'qr_portatil' => 'Portátil no encontrado con el serial: ' . $serial
            ]);
        }

        return $portatil;
    }

    private function buscarVehiculoPorQr($qrVehiculo)
    {
        $placa = str_replace('VEHICULO_', '', $qrVehiculo);
        $vehiculo = Vehiculo::buscarPorPlaca($placa);
        
        if (!$vehiculo) {
            throw ValidationException::withMessages([
                'qr_vehiculo' => 'Vehículo no encontrado con la placa: ' . $placa
            ]);
        }

        return $vehiculo;
    }

    // Métodos AJAX para actualizar datos en tiempo real
    public function accesosActivos()
    {
        $accesos = Acceso::activos()
            ->with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada')
            ->get();

        return response()->json($accesos);
    }

    public function historialDelDia()
    {
        $historial = Acceso::delDia()
            ->with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada')
            ->get();

        return response()->json($historial);
    }

    public function estadisticas()
    {
        $estadisticas = Acceso::estadisticasDelDia();
        return response()->json($estadisticas);
    }

    public function buscarPersona(Request $request)
    {
        $request->validate([
            'qr_persona' => 'required|string'
        ]);

        try {
            $persona = $this->buscarPersonaPorQr($request->qr_persona);
            
            return $this->formatearRespuestaPersona($persona);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al buscar persona', [
                'error' => $e->getMessage(),
                'qr_persona' => $request->qr_persona
            ]);
            
            return response()->json([
                'message' => 'Error interno del sistema'
            ], 500);
        }
    }

    /**
     * Buscar persona por número de cédula directamente
     */
    public function buscarPersonaPorCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|min:5|max:20'
        ], [
            'cedula.required' => 'El número de cédula es obligatorio',
            'cedula.min' => 'El número de cédula debe tener al menos 5 caracteres',
            'cedula.max' => 'El número de cédula no puede tener más de 20 caracteres'
        ]);

        try {
            $cedula = trim($request->cedula);
            
            // Buscar persona directamente por documento
            $persona = Persona::buscarPorDocumento($cedula);
            
            if (!$persona) {
                throw ValidationException::withMessages([
                    'cedula' => 'No se encontró ninguna persona con el documento: ' . $cedula
                ]);
            }

            return $this->formatearRespuestaPersona($persona);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al buscar persona por cédula', [
                'error' => $e->getMessage(),
                'cedula' => $request->cedula
            ]);
            
            return response()->json([
                'message' => 'Error interno del sistema'
            ], 500);
        }
    }

    /**
     * Formatear respuesta de persona con información completa
     */
    private function formatearRespuestaPersona($persona)
    {
        // Verificar si tiene acceso activo
        $accesoActivo = $persona->getAccesoActivo();
        
        // Cargar relaciones necesarias
        $persona->load(['portatiles', 'vehiculos']);
        
        // Información del portátil asociado (si tiene)
        $portatilInfo = null;
        if ($persona->portatiles->isNotEmpty()) {
            $portatil = $persona->portatiles->first();
            $portatilInfo = [
                'portatil_id' => $portatil->portatil_id,
                'marca' => $portatil->marca,
                'modelo' => $portatil->modelo,
                'serial' => $portatil->serial,
                'descripcion' => $portatil->marca . ' ' . $portatil->modelo . ' (Serial: ' . $portatil->serial . ')'
            ];
        }
        
        // Información del vehículo asociado (si tiene)
        $vehiculoInfo = null;
        if ($persona->vehiculos->isNotEmpty()) {
            $vehiculo = $persona->vehiculos->first();
            $vehiculoInfo = [
                'id' => $vehiculo->id,
                'tipo' => $vehiculo->tipo,
                'placa' => $vehiculo->placa,
                'descripcion' => $vehiculo->tipo . ' - Placa: ' . $vehiculo->placa
            ];
        }
        
        // Construir respuesta completa
        $response = [
            'persona' => [
                'idPersona' => $persona->idPersona,
                'Nombre' => $persona->Nombre,
                'documento' => $persona->documento,
                'TipoPersona' => $persona->TipoPersona,
                'correo' => $persona->correo
            ],
            'tiene_acceso_activo' => $accesoActivo ? true : false,
            'es_entrada' => !$accesoActivo,
            'es_salida' => $accesoActivo ? true : false,
            'portatil_asociado' => $portatilInfo,
            'vehiculo_asociado' => $vehiculoInfo,
            'tiene_portatil' => $portatilInfo !== null,
            'tiene_vehiculo' => $vehiculoInfo !== null,
            'mensaje_accion' => $accesoActivo ? 'SALIDA detectada' : 'ENTRADA detectada'
        ];
        
        // Si tiene acceso activo (es salida), agregar datos del acceso
        if ($accesoActivo) {
            $accesoActivo->load(['portatil', 'vehiculo']);
            
            $response['acceso_activo'] = [
                'idAcceso' => $accesoActivo->idAcceso,
                'fecha_entrada' => $accesoActivo->fecha_entrada->format('Y-m-d H:i:s'),
                'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i'),
                'portatil_entrada' => $accesoActivo->portatil ? [
                    'portatil_id' => $accesoActivo->portatil->portatil_id,
                    'marca' => $accesoActivo->portatil->marca,
                    'modelo' => $accesoActivo->portatil->modelo,
                    'serial' => $accesoActivo->portatil->serial,
                    'descripcion' => $accesoActivo->portatil->marca . ' ' . $accesoActivo->portatil->modelo . ' (Serial: ' . $accesoActivo->portatil->serial . ')'
                ] : null,
                'vehiculo_entrada' => $accesoActivo->vehiculo ? [
                    'id' => $accesoActivo->vehiculo->id,
                    'tipo' => $accesoActivo->vehiculo->tipo,
                    'placa' => $accesoActivo->vehiculo->placa,
                    'descripcion' => $accesoActivo->vehiculo->tipo . ' - Placa: ' . $accesoActivo->vehiculo->placa
                ] : null,
                'requiere_verificacion_portatil' => $accesoActivo->portatil_id !== null,
                'requiere_verificacion_vehiculo' => $accesoActivo->vehiculo_id !== null
            ];
        }
        
        return response()->json($response);
    }
}
