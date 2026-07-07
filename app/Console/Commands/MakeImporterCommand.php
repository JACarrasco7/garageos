<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeImporterCommand extends Command
{
    protected $signature = 'user:make-importer {email} {--force : Sin confirmación}';
    protected $description = 'Convertir usuario a importador';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Usuario no encontrado: {$email}");
            return self::FAILURE;
        }

        if (! $this->option('force')) {
            if (! $this->confirm("¿Convertir a {$user->name} ({$email}) en importador?")) {
                return self::SUCCESS;
            }
        }

        $user->syncRoles(['importer']);

        $this->info("Usuario {$user->name} convertido a importador.");
        return self::SUCCESS;
    }
}
