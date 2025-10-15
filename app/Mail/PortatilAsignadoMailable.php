<?php

namespace App\Mail;

use App\Models\Portatil;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortatilAsignadoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Portatil $portatil
    ) {
    }

    public function build(): self
    {
        // Cargar la persona asociada
        $this->portatil->load('persona');
        
        $mail = $this->subject('Portátil Asignado - CTAccess')
            ->view('emails.portatil_asignado', [
                'portatil' => $this->portatil,
                'persona' => $this->portatil->persona,
            ]);

        // Adjuntar PNG del QR del portátil si existe
        $qrUrl = (string) ($this->portatil->qrCode ?? '');
        if ($qrUrl) {
            // Convertir URL tipo /storage/qrcodes/foo.png a ruta en disco public
            $relative = ltrim(str_replace('/storage/', '', $qrUrl), '/');
            $fullPath = storage_path('app/public/' . $relative);
            if (is_file($fullPath)) {
                $mail->attach($fullPath, [
                    'as' => 'qr_portatil_' . $this->portatil->serial . '.png',
                    'mime' => 'image/png',
                ]);
            }
        }

        return $mail;
    }
}
