<?php

namespace App\Events;

use App\Models\Acceso;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Evento que se dispara cuando se registra un nuevo acceso.
 * Se emite por WebSocket para actualizar la vista en tiempo real.
 * Usa ShouldBroadcastNow para transmisión inmediata sin colas.
 */
class AccesoRegistrado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $acceso;

    /**
     * Create a new event instance.
     */
    public function __construct(Acceso $acceso)
    {
        $this->acceso = $acceso;
        
        Log::info('📡 AccesoRegistrado evento creado', [
            'acceso_id' => $acceso->id,
            'estado' => $acceso->estado
        ]);
    }

    /**
     * Canal público para todos los usuarios
     */
    public function broadcastOn(): Channel
    {
        return new Channel('accesos');
    }

    /**
     * Nombre del evento en el frontend
     */
    public function broadcastAs(): string
    {
        return 'acceso.registrado';
    }

    /**
     * Datos que se envían al frontend
     */
    public function broadcastWith(): array
    {
        // Cargar relación de persona si no está cargada
        if (!$this->acceso->relationLoaded('persona')) {
            $this->acceso->load('persona');
        }

        return [
            'id' => $this->acceso->id,
            'persona' => [
                'id' => $this->acceso->persona->idPersona,
                'nombre' => $this->acceso->persona->Nombre,
                'documento' => $this->acceso->persona->documento,
                'tipo' => $this->acceso->persona->TipoPersona,
            ],
            'hora_entrada' => $this->acceso->fecha_entrada->format('H:i:s'),
            'hora_salida' => $this->acceso->fecha_salida?->format('H:i:s'),
            'estado' => $this->acceso->estado,
            'tipo_acceso' => $this->acceso->fecha_salida ? 'salida' : 'entrada',
            'timestamp' => $this->acceso->fecha_entrada->toDateTimeString(),
        ];
    }
}
