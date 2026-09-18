<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;

class InstallController extends Controller
{
    /**
     * Halaman Wizard Instalasi Aplikasi (Replikasi att-admin-v12)
     */
    public function index()
    {
        $requirements = [
            'PHP Version (>= 8.2)' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO SQLite Extension' => extension_loaded('pdo_sqlite'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'XML / SimpleXML Extension' => extension_loaded('xml'),
            'cURL Extension' => extension_loaded('curl'),
            'GD Extension' => extension_loaded('gd'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'Directory storage/ Writable' => is_writable(storage_path()),
            'Directory bootstrap/cache/ Writable' => is_writable(base_path('bootstrap/cache')),
        ];

        $allRequirementsMet = !in_array(false, [
            $requirements['PHP Version (>= 8.2)'],
            $requirements['PDO Extension'],
            $requirements['Mbstring Extension'],
            $requirements['OpenSSL Extension'],
            $requirements['Directory storage/ Writable'],
            $requirements['Directory bootstrap/cache/ Writable'],
        ]);

        return view('install', compact('requirements', 'allRequirementsMet'));
    }

    /**
     * Proses Instalasi, Konfigurasi Database, Migrasi, & Akun Admin
     */
    public function process(Request $request)
    {
        $request->validate([
            'app_url' => 'required|url',
            'db_connection' => 'required|in:sqlite,mysql',
            'admin_name' => 'required|string|max:100',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        $dbConn = $request->db_connection;

        if ($dbConn === 'mysql') {
            $request->validate([
                'db_host' => 'required',
                'db_port' => 'required|numeric',
                'db_name' => 'required',
                'db_user' => 'required',
            ]);
        }

        try {
            // 1. Siapkan update data .env
            $envUpdates = [
                'APP_URL' => rtrim($request->app_url, '/'),
                'DB_CONNECTION' => $dbConn,
            ];

            if ($dbConn === 'mysql') {
                $envUpdates['DB_HOST'] = $request->db_host;
                $envUpdates['DB_PORT'] = $request->db_port;
                $envUpdates['DB_DATABASE'] = $request->db_name;
                $envUpdates['DB_USERNAME'] = $request->db_user;
                $envUpdates['DB_PASSWORD'] = $request->db_password ?? '';
            } else {
                $dbPath = $request->input('sqlite_database', 'asystem_interview');
                $envUpdates['DB_DATABASE'] = $dbPath;
            }

            $this->updateEnv($envUpdates);

            // 2. Clear cache konfigurasi
            Artisan::call('config:clear');

            // 3. Konfigurasi runtime database untuk migrasi
            if ($dbConn === 'mysql') {
                config([
                    'database.default' => 'mysql',
                    'database.connections.mysql.host' => $request->db_host,
                    'database.connections.mysql.port' => $request->db_port,
                    'database.connections.mysql.database' => $request->db_name,
                    'database.connections.mysql.username' => $request->db_user,
                    'database.connections.mysql.password' => $request->db_password ?? '',
                ]);
            } else {
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $envUpdates['DB_DATABASE'],
                ]);
            }

            DB::purge($dbConn);

            // Tes koneksi database
            DB::connection($dbConn)->getPdo();

            // 4. Jalankan migrasi database
            Artisan::call('migrate', [
                '--force' => true,
                '--database' => $dbConn,
            ]);

            // 5. Buat atau perbarui akun Super Administrator
            User::updateOrCreate(
                ['email' => $request->admin_email],
                [
                    'name' => $request->admin_name,
                    'password' => Hash::make($request->admin_password),
                    'role' => 'admin',
                    'job_title' => 'ADMINISTRATOR PUSAT',
                    'area' => 'PUSAT',
                ]
            );

            // 6. Buat flag file .installed
            if (!File::exists(storage_path('app'))) {
                File::makeDirectory(storage_path('app'), 0755, true, true);
            }
            File::put(storage_path('app/.installed'), 'Installed successfully on ' . now()->toDateTimeString() . ' via Web Installer');

            return redirect('/login')->with('success', 'Instalasi ASystem Support System berhasil! Silakan login menggunakan akun administrator Anda.');

        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal melakukan instalasi: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk memperbarui nilai di file .env
     */
    private function updateEnv(array $values)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envFile);
            } else {
                touch($envFile);
            }
        }

        $content = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            $formattedValue = (str_contains($value, ' ') || str_contains($value, '#')) ? "\"{$value}\"" : $value;
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $content);
            } else {
                $content .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envFile, $content);
    }
}
