<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portatiles extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'portatil_id';
    protected $fillable = ['persona_id', 'serial', 'qrCode', 'marca', 'modelo'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'idPersona');
    }

    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'portatil_id', 'portatil_id');
    }

    // Métodos para QR
    public static function buscarPorQr($qrCode)
    {
        return self::where('qrCode', $qrCode)->first();
    }

    public function perteneceAPersona($personaId)
    {
        return $this->persona_id == $personaId;
    }

    public function getInfoCompleta()
    {
        return [
            'portatil' => $this,
            'persona' => $this->persona,
            'accesos_recientes' => $this->accesos()->latest()->take(5)->get()
        ];
    }
}
