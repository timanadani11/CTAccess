<?php

namespace App\Mail;

use App\Models\Persona;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonaQrMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Persona $persona)
    {
    }

    public function build(): self
    {
        $mail = $this->subject('Tu código QR de registro')
            ->view('emails.persona_qr', [
                'persona' => $this->persona,
            ]);

        // Adjuntar PNG del QR si existe
        $qrUrl = (string) ($this->persona->qrCode ?? '');
        if ($qrUrl) {
            // Convertir URL tipo /storage/qrcodes/foo.png a ruta en disco public
            $relative = ltrim(str_replace('/storage/', '', $qrUrl), '/');
            $fullPath = storage_path('app/public/' . $relative);
            if (is_file($fullPath)) {
                $mail->attach($fullPath, [
                    'as' => 'qr_persona.png',
                    'mime' => 'image/png',
                ]);
            }
        }

        return $mail;
    }
}
