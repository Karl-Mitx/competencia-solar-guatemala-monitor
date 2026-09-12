<?php

// Local packaging only. Never upload this script or the generated SQL/credentials to htdocs.
require __DIR__.'/../vendor/autoload.php';

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$root = dirname(__DIR__);
$workspace = dirname($root);
$stage = $workspace.'/.local/infinityfree/htdocs';
$output = $workspace.'/output/deploy';
$mode = $argv[1] ?? 'prepare';

function copyTree(string $from, string $to): void
{
    if (is_link($from)) {
        throw new RuntimeException('Symlink not allowed in package: '.$from);
    }
    if (is_dir($from)) {
        if (! is_dir($to)) {
            mkdir($to, 0775, true);
        }
        foreach (scandir($from) as $entry) {
            if (in_array($entry, ['.', '..', '.gitignore', 'hot'], true)) {
                continue;
            }
            copyTree($from.'/'.$entry, $to.'/'.$entry);
        }
    } elseif (! copy($from, $to)) {
        throw new RuntimeException('Cannot copy '.$from);
    }
}

if ($mode === 'prepare') {
    if (is_dir($stage)) {
        throw new RuntimeException('A staging package already exists; preserve it and use a fresh staging directory for another release.');
    }
    mkdir($stage, 0775, true);
    if (! is_dir($output)) {
        mkdir($output, 0775, true);
    }
    foreach (['app', 'config', 'routes', 'resources', 'lang', 'public', 'database/migrations', 'database/seeders', 'database/factories'] as $path) {
        copyTree($root.'/'.$path, $stage.'/'.$path);
    }
    foreach (['bootstrap/cache', 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'storage/app/private', 'storage/app/public'] as $path) {
        if (! is_dir($stage.'/'.$path)) {
            mkdir($stage.'/'.$path, 0775, true);
        }
        file_put_contents($stage.'/'.$path.'/.gitkeep', '');
    }
    foreach (['bootstrap/app.php', 'bootstrap/providers.php', 'composer.json', 'composer.lock', 'artisan'] as $path) {
        copyTree($root.'/'.$path, $stage.'/'.$path);
    }
    copyTree(__DIR__.'/infinityfree.htaccess', $stage.'/.htaccess');
    // Defense in depth: protect private directories even if the root rewrite changes.
    foreach (['app', 'bootstrap', 'config', 'database', 'lang', 'resources', 'routes', 'storage', 'vendor'] as $path) {
        if (! is_dir($stage.'/'.$path)) {
            mkdir($stage.'/'.$path, 0775, true);
        }
        file_put_contents($stage.'/'.$path.'/.htaccess', "Require all denied\n");
    }
    $key = 'base64:'.base64_encode(random_bytes(32));
    $env = "APP_NAME=\"Solaris Guatemala\"\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_KEY={$key}\nAPP_URL=https://mapasolargt5.gt.tc\nAPP_LOCALE=es\nAPP_FALLBACK_LOCALE=es\nDB_CONNECTION=mysql\nDB_HOST=sql312.infinityfree.com\nDB_PORT=3306\nDB_DATABASE=if0_42898262_solaris\nDB_USERNAME=if0_42898262\nDB_PASSWORD=REEMPLAZAR_CON_PASSWORD_MYSQL\nSESSION_DRIVER=file\nSESSION_SECURE_COOKIE=true\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\nLOG_CHANNEL=single\nLOG_LEVEL=error\nMAIL_MAILER=log\nDEMO_DATA=true\n";
    file_put_contents($stage.'/.env', $env);

    // Generate demo data in a NEW in-memory SQLite connection, never the user's database.
    $app = require $root.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    config(['database.default' => 'package_export', 'database.connections.package_export' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true]]);
    Artisan::call('migrate', ['--database' => 'package_export', '--force' => true]);
    Artisan::call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
    $password = bin2hex(random_bytes(12));
    User::create(['name' => 'Jerelyn Marín', 'email' => 'jmarinm4@miumg.edu.gt', 'password' => $password]);
    $source = DB::connection('package_export');

    // Compile the real migrations with Laravel's MySQL grammar without a remote connection.
    $mysql = new class($source->getPdo(), 'solaris', '', ['charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'engine' => 'InnoDB']) extends MySqlConnection
    {
        public function isMaria()
        {
            return false;
        }

        public function getServerVersion(): string
        {
            return '8.0.0';
        }
    };
    $mysql->useDefaultSchemaGrammar();
    Schema::swap($mysql->getSchemaBuilder());
    $queries = $mysql->pretend(function () use ($root) {
        foreach (glob($root.'/database/migrations/*.php') as $file) {
            (require $file)->up();
        }
    });
    $sql = "-- Solaris demo. Import ONLY into the newly created empty database.\n-- No DROP, TRUNCATE or CREATE DATABASE statements.\nSET NAMES utf8mb4;\nSET time_zone = '+00:00';\n";
    $sql .= "CREATE TABLE `migrations` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `migration` VARCHAR(255) NOT NULL, `batch` INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";
    foreach ($queries as $query) {
        if ($query['bindings'] !== []) {
            throw new RuntimeException('Unexpected schema bindings');
        }
        $sql .= $query['query'].";\n";
    }
    $counts = [];
    foreach (['migrations', 'departments', 'solar_panels', 'solar_farms', 'farm_panel', 'generation_records', 'alerts', 'projections', 'users'] as $table) {
        $rows = $source->table($table)->orderBy('id')->get();
        $counts[$table] = $rows->count();
        foreach ($rows as $row) {
            $values = (array) $row;
            $columns = implode(', ', array_map(fn ($column) => '`'.$column.'`', array_keys($values)));
            // UTF-8 hex literals avoid SQL-mode-dependent escaping of text.
            $literals = array_map(fn ($value) => $value === null ? 'NULL' : (is_int($value) || is_float($value) ? (string) $value : "CONVERT(X'".bin2hex((string) $value)."' USING utf8mb4)"), array_values($values));
            $sql .= 'INSERT INTO `'.$table.'` ('.$columns.') VALUES ('.implode(', ', $literals).");\n";
        }
    }
    file_put_contents($output.'/solaris-inicial.sql', $sql);
    file_put_contents($output.'/ACCESO-ADMIN-PRIVADO.txt', "Acceso de administrador creado para ESTA importación. No subir este archivo ni publicarlo.\nURL: https://mapasolargt5.gt.tc/login\nCorreo: jmarinm4@miumg.edu.gt\nContraseña: {$password}\n");
    file_put_contents($output.'/conteos.json', json_encode($counts, JSON_PRETTY_PRINT));
    echo "Staging and initial SQL prepared. Credentials saved privately; not printed.\n";
    echo json_encode($counts, JSON_PRETTY_PRINT)."\n";
} elseif ($mode === 'finalize') {
    if (! is_file($stage.'/vendor/autoload.php') || ! is_file($stage.'/public/build/manifest.json')) {
        throw new RuntimeException('Production dependencies or frontend build missing');
    }
    $zip = new ZipArchive;
    $zipPath = $output.'/solaris-infinityfree.zip';
    if (is_file($zipPath)) {
        throw new RuntimeException('Release ZIP already exists; do not overwrite it silently.');
    }
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::EXCL);
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS));
    $count = 0;
    foreach ($files as $file) {
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($stage) + 1));
        if ($file->isLink()) {
            throw new RuntimeException('Unexpected symlink: '.$relative);
        }
        if (str_ends_with($relative, '.php') && $file->getSize() > 1000000) {
            throw new RuntimeException('PHP file exceeds host limit: '.$relative);
        }
        if (preg_match('~(^|/)(node_modules|tests|\.git)(/|$)|\.sqlite|\.sql$|\.env\.(atlas|testing|local)$~i', $relative)) {
            throw new RuntimeException('Unwanted file: '.$relative);
        }
        $zip->addFile($file->getPathname(), $relative);
        $count++;
    }
    $zip->close();
    echo "Package created with {$count} files; ".filesize($zipPath)." bytes.\n";
} else {
    throw new InvalidArgumentException('Use prepare or finalize');
}
