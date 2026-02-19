<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AppInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options allow non-interactive usage.
     */
    protected $signature = 'app:install
                            {--db-host= : Database host}
                            {--db-database= : Database name}
                            {--db-username= : Database user}
                            {--db-password= : Database password}
                            {--app-name= : Application name}
                            {--admin-email= : Admin email}
                            {--admin-password= : Admin password}
                            {--force : Skip prompts}
    ';

    /**
     * The console command description.
     */
    protected $description = 'Interactive installer for SamzTune-Up (migrate, seed, create admin)';

    public function handle()
    {
        $this->info('SamzTune-Up installer — starting');

        $force = $this->option('force');

        // Collect configuration (interactive if needed)
        $dbHost = $this->option('db-host') ?: $this->askUnlessForced('DB host', 'localhost', $force);
        $dbName = $this->option('db-database') ?: $this->askUnlessForced('DB name', 'samztune_up', $force);
        $dbUser = $this->option('db-username') ?: $this->askUnlessForced('DB username', 'root', $force);
        $dbPass = $this->option('db-password') ?? ($force ? '' : $this->secret('DB password (leave empty for none)'));

        $appName = $this->option('app-name') ?: $this->askUnlessForced('Application name', 'SamzTune-Up', $force);

        $adminEmail = $this->option('admin-email') ?: $this->askUnlessForced('Admin email', 'admin@samztune-up.com', $force);
        $adminPassword = $this->option('admin-password') ?: ($force ? 'admin123' : $this->secret('Admin password (min 8 chars)'));

        if (! $force) {
            if (! $this->confirm("Proceed with installation using DB '{$dbUser}@{$dbHost}/{$dbName}' and admin '{$adminEmail}'?")) {
                $this->info('Installation aborted.');
                return 1;
            }
        }

        // Update .env values
        $this->setEnvValue(base_path('.env'), 'DB_HOST', $dbHost);
        $this->setEnvValue(base_path('.env'), 'DB_DATABASE', $dbName);
        $this->setEnvValue(base_path('.env'), 'DB_USERNAME', $dbUser);
        $this->setEnvValue(base_path('.env'), 'DB_PASSWORD', $dbPass ?? '');
        $this->setEnvValue(base_path('.env'), 'APP_NAME', $appName);
        $this->setEnvValue(base_path('.env'), 'APP_ENV', 'production');
        $this->setEnvValue(base_path('.env'), 'APP_DEBUG', 'false');

        // Test DB connection
        $this->line('Testing database connection...');
        try {
            $pdo = new \PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass ?? '');
            $pdo = null;
            $this->info('Database connection OK');
        } catch (\PDOException $e) {
            $this->error('Database connection failed: '.$e->getMessage());
            return 2;
        }

        // Run migrations + seed
        $this->callSilent('migrate', ['--force' => true]);
        $this->info('Migrations executed');

        $this->callSilent('db:seed', ['--force' => true]);
        $this->info('Database seeded');

        // Create admin user (if not exists)
        $this->createAdminUser($adminEmail, $adminPassword);

        // Generate key
        if (! config('app.key')) {
            $this->call('key:generate');
        }

        // Storage link
        $this->call('storage:link');

        // Clear caches
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info('Installation completed successfully.');
        $this->info("Admin: {$adminEmail}");

        return 0;
    }

    private function askUnlessForced(string $question, $default = null, bool $force = false)
    {
        if ($force) return $default;
        return $this->ask($question, $default);
    }

    private function setEnvValue($path, $key, $value)
    {
        if (! file_exists($path)) return;
        $content = file_get_contents($path);
        $escapedValue = str_replace(['"', "'"], ['\\"', "\\'"], (string) $value);

        if (preg_match("/^{$key}=.*$/m", $content)) {
            $content = preg_replace("/^{$key}=.*$/m", "{$key}='".$escapedValue."'", $content);
        } else {
            $content .= "\n{$key}='".$escapedValue."'";
        }

        file_put_contents($path, $content);
    }

    private function createAdminUser($email, $password)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            $this->info('Admin user already exists — updating password');
            $user->password = Hash::make($password);
            $user->save();
            return;
        }

        User::create([
            'name' => 'Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created');
    }
}
