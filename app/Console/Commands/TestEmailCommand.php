<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PersonaQrMailable;
use App\Models\Persona;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {--persona-id= : ID de la persona para probar el email}';
    protected $description = 'Prueba el envío de correos con QR de personas';

    public function handle()
    {
        $personaId = $this->option('persona-id');
        
        if ($personaId) {
            $persona = Persona::find($personaId);
            if (!$persona) {
                $this->error("No se encontró la persona con ID: {$personaId}");
                return 1;
            }
        } else {
            // Buscar la primera persona con correo
            $persona = Persona::whereNotNull('correo')->first();
            if (!$persona) {
                $this->error('No se encontró ninguna persona con correo electrónico');
                return 1;
            }
        }

        $this->info("Probando envío de correo a: {$persona->correo}");
        $this->info("Persona: {$persona->Nombre}");
        
        try {
            Mail::to($persona->correo)->send(new PersonaQrMailable($persona));
            
            $this->info('✅ Correo enviado exitosamente!');
            
            if (config('mail.default') === 'log') {
                $this->warn('📧 NOTA: El correo se guardó en storage/logs/laravel.log porque MAIL_MAILER=log');
                $this->info('Para enviar correos reales, configura SMTP en tu archivo .env');
            }
            
            return 0;
            
        } catch (\Throwable $e) {
            $this->error('❌ Error enviando correo: ' . $e->getMessage());
            return 1;
        }
    }
}
