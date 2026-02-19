<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    public function index()
    {
        // Check if already installed
        if ($this->isInstalled()) {
            return redirect('/')->with('info', 'Aplikasi sudah terinstall!');
        }

        // Run pre-installation requirement checks and pass results to view
        $requirements = $this->checkRequirements();

        return view('install.index', compact('requirements'));
    }

    public function install(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'db_host' => 'required',
                'db_database' => 'required',
                'db_username' => 'required',
                'db_password' => 'nullable',
                'app_name' => 'required',
                'admin_email' => 'required|email',
                'admin_password' => 'required|min:8',
            ]);

            // Update .env file
            $this->updateEnvFile($request->all());

            // Test database connection
            $this->testDatabaseConnection($request->only(['db_host', 'db_database', 'db_username', 'db_password']));

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);

            // Create admin user
            $this->createAdminUser($request->only(['admin_email', 'admin_password']));

            // Generate app key if not exists
            if (!config('app.key')) {
                Artisan::call('key:generate');
            }

            // Clear cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Create installed sentinel file so auto-deploy can detect installation
            try {
                file_put_contents(storage_path('framework/installed'), now()->toDateTimeString());
            } catch (\Exception $e) {
                // ignore write failures
            }

            return redirect('/install/success');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Installasi gagal: ' . $e->getMessage()]);
        }
    }

    public function success()
    {
        return view('install.success');
    }

    private function isInstalled()
    {
        try {
            // Prefer sentinel file check
            if (file_exists(storage_path('framework/installed'))) {
                return true;
            }

            // Fallback: Check if migrations table exists and has records
            return Schema::hasTable('migrations') && DB::table('migrations')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Pre-installation requirement checks.
     * Returns array: ['checks' => [...], 'all_ok' => bool]
     */
    private function checkRequirements()
    {
        $checks = [];

        // PHP version
        $phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
        $checks[] = [
            'key' => 'php_version',
            'label' => 'PHP >= 8.2',
            'ok' => $phpOk,
            'critical' => true,
            'value' => PHP_VERSION,
            'help' => $phpOk ? '' : 'Upgrade PHP ke versi 8.2 atau lebih baru.'
        ];

        // Required PHP extensions
        $requiredExt = ['fileinfo','gd','mbstring','openssl','tokenizer','xml','ctype','json','pdo_mysql'];
        foreach ($requiredExt as $ext) {
            $loaded = extension_loaded($ext);
            $checks[] = [
                'key' => 'ext_'.$ext,
                'label' => "PHP extension: {$ext}",
                'ok' => $loaded,
                'critical' => in_array($ext, ['fileinfo','pdo_mysql','mbstring','openssl','gd']),
                'help' => $loaded ? '' : "Aktifkan ekstensi PHP: {$ext}"
            ];
        }

        // Writable directories
        $paths = [storage_path('app'), storage_path('framework'), storage_path('logs'), base_path('bootstrap/cache')];
        foreach ($paths as $p) {
            $ok = is_dir($p) && is_writable($p);
            $checks[] = [
                'key' => 'writable_'.md5($p),
                'label' => 'Writable: '.str_replace(base_path(), '', $p),
                'ok' => $ok,
                'critical' => true,
                'help' => $ok ? '' : "Beri permission writable pada: {$p}"
            ];
        }

        // public/storage symlink
        $publicStorage = public_path('storage');
        $symlinkOk = is_link($publicStorage) && file_exists($publicStorage);
        $checks[] = [
            'key' => 'public_storage',
            'label' => 'Storage symlink (public/storage)',
            'ok' => $symlinkOk,
            'critical' => false,
            'help' => $symlinkOk ? '' : 'Jalankan: php artisan storage:link setelah instalasi'
        ];

        $allOk = true;
        foreach ($checks as $c) {
            if ($c['critical'] && ! $c['ok']) { $allOk = false; break; }
        }

        return ['checks' => $checks, 'all_ok' => $allOk];
    }

    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');

        // Update database config
        $this->setEnvValue($envPath, 'DB_HOST', $data['db_host']);
        $this->setEnvValue($envPath, 'DB_DATABASE', $data['db_database']);
        $this->setEnvValue($envPath, 'DB_USERNAME', $data['db_username']);
        $this->setEnvValue($envPath, 'DB_PASSWORD', $data['db_password'] ?? '');

        // Update app config
        $this->setEnvValue($envPath, 'APP_NAME', $data['app_name']);
        $this->setEnvValue($envPath, 'APP_ENV', 'production');
        $this->setEnvValue($envPath, 'APP_DEBUG', 'false');
    }

    private function setEnvValue($path, $key, $value)
    {
        $content = file_get_contents($path);

        // Escape special characters in value
        $escapedValue = str_replace(['"', "'"], ['\"', "\'"], $value);

        // Update or add the key
        if (preg_match("/^{$key}=.*$/m", $content)) {
            $content = preg_replace("/^{$key}=.*$/m", "{$key}=\"{$escapedValue}\"", $content);
        } else {
            $content .= "\n{$key}=\"{$escapedValue}\"";
        }

        file_put_contents($path, $content);
    }

    private function testDatabaseConnection($dbConfig)
    {
        try {
            $pdo = new \PDO(
                "mysql:host={$dbConfig['db_host']};dbname={$dbConfig['db_database']}",
                $dbConfig['db_username'],
                $dbConfig['db_password'] ?? ''
            );
            $pdo = null;
        } catch (\PDOException $e) {
            throw new \Exception('Koneksi database gagal: ' . $e->getMessage());
        }
    }

    private function createAdminUser($adminData)
    {
        // Create admin user using seeder or directly
        $adminSeeder = new \Database\Seeders\AdminUserSeeder();
        $adminSeeder->run();
    }
}
