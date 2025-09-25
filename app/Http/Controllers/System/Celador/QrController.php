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
        $portatilId = null;
        $vehiculoId = null;

        // Verificar portátil si se proporcionó QR
        if ($request->qr_portatil) {
            $portatil = $this->buscarPortatilPorQr($request->qr_portatil);
            
            if (!$portatil->perteneceAPersona($persona->idPersona)) {
                throw ValidationException::withMessages([
                    'qr_portatil' => 'El portátil no pertenece a esta persona.'
                ]);
            }
            
            $portatilId = $portatil->portatil_id;
        }

        // Verificar vehículo si se proporcionó QR
        if ($request->qr_vehiculo) {
            $vehiculo = $this->buscarVehiculoPorQr($request->qr_vehiculo);
            
            if (!$vehiculo->perteneceAPersona($persona->idPersona)) {
                throw ValidationException::withMessages([
                    'qr_vehiculo' => 'El vehículo no pertenece a esta persona.'
                ]);
            }
            
            $vehiculoId = $vehiculo->id;
        }

        // Registrar entrada
        $acceso = Acceso::registrarEntrada(
            $persona->idPersona,
            $portatilId,
            $vehiculoId,
            $usuario->idUsuariio
        );

        return back()->with('success', [
            'tipo' => 'entrada',
            'mensaje' => 'Entrada registrada exitosamente',
            'persona' => $persona->Nombre,
            'documento' => $persona->documento,
            'hora' => $acceso->fecha_entrada->format('H:i:s'),
            'portatil' => $portatilId ? 'Sí' : 'No',
            'vehiculo' => $vehiculoId ? 'Sí' : 'No'
        ]);
    }

    private function procesarSalida($persona, $accesoActivo, $request, $usuario)
    {
        $errores = [];

        // Verificar portátil en la salida
        if ($accesoActivo->portatil_id) {
            if (!$request->qr_portatil) {
                $errores[] = 'Debe escanear el QR del portátil que registró en la entrada.';
            } else {
                $portatil = $this->buscarPortatilPorQr($request->qr_portatil);
                
                if ($portatil->portatil_id != $accesoActivo->portatil_id) {
                    $errores[] = 'El portátil escaneado no coincide con el registrado en la entrada.';
                }
            }
        }

        // Verificar vehículo en la salida
        if ($accesoActivo->vehiculo_id) {
            if (!$request->qr_vehiculo) {
                $errores[] = 'Debe escanear el QR del vehículo que registró en la entrada.';
            } else {
                $vehiculo = $this->buscarVehiculoPorQr($request->qr_vehiculo);
                
                if ($vehiculo->id != $accesoActivo->vehiculo_id) {
                    $errores[] = 'El vehículo escaneado no coincide con el registrado en la entrada.';
                }
            }
        }

        // Si hay errores, registrar incidencia
        if (!empty($errores)) {
            $descripcion = 'Inconsistencias en salida: ' . implode(' ', $errores);
            $incidencia = $accesoActivo->marcarIncidencia($descripcion, $usuario->idUsuariio);
            
            return back()->with('warning', [
                'tipo' => 'incidencia',
                'mensaje' => 'Se registró una incidencia en la salida',
                'persona' => $persona->Nombre,
                'errores' => $errores,
                'incidencia_id' => $incidencia->incidenciaId
            ]);
        }

        // Registrar salida exitosa
        $accesoActivo->marcarSalida($usuario->idUsuariio);

        return back()->with('success', [
            'tipo' => 'salida',
            'mensaje' => 'Salida registrada exitosamente',
            'persona' => $persona->Nombre,
            'documento' => $persona->documento,
            'hora_entrada' => $accesoActivo->fecha_entrada->format('H:i:s'),
            'hora_salida' => $accesoActivo->fecha_salida->format('H:i:s'),
            'duracion' => $accesoActivo->duracion
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
            
            // Verificar si tiene acceso activo
            $accesoActivo = $persona->getAccesoActivo();
            
            return response()->json([
                'persona' => [
                    'Nombre' => $persona->Nombre,
                    'documento' => $persona->documento,
                    'TipoPersona' => $persona->TipoPersona,
                    'correo' => $persona->correo
                ],
                'tiene_acceso_activo' => $accesoActivo ? true : false,
                'acceso_activo' => $accesoActivo ? [
                    'fecha_entrada' => $accesoActivo->fecha_entrada,
                    'portatil_id' => $accesoActivo->portatil_id,
                    'vehiculo_id' => $accesoActivo->vehiculo_id
                ] : null
            ]);
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
}
