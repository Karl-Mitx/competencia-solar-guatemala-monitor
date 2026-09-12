<?php

// Run locally after npm run build. This update intentionally excludes .env and SQL.
$root = dirname(__DIR__);
$destination = dirname($root).'/output/deploy/solaris-mapa-atlas.zip';
$files = [
    'app/Services/AnalyticsService.php',
    'resources/views/dashboard.blade.php',
    'resources/views/map.blade.php',
    'resources/views/partials/territory-map.blade.php',
    'resources/views/layouts/app.blade.php',
    'public/build/manifest.json',
];
if (($argv[1] ?? '') === 'features') {
    $destination = dirname($root).'/output/deploy/solaris-tema-correo.zip';
    $files = array_merge($files, [
        'app/Http/Controllers/DashboardController.php',
        'app/Http/Controllers/ReportEmailController.php',
        'app/Http/Requests/EmailReportRequest.php',
        'app/Services/ReportCsv.php',
        'app/Mail/DepartmentReport.php',
        'resources/views/reports.blade.php',
        'resources/views/emails/department-report.blade.php',
        'routes/web.php',
        'config/mail.php',
    ]);
}
$manifest = json_decode(file_get_contents($root.'/public/build/manifest.json'), true, flags: JSON_THROW_ON_ERROR);
foreach ($manifest as $entry) {
    $files[] = 'public/build/'.$entry['file'];
}
$zip = new ZipArchive;
if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    throw new RuntimeException('Cannot create map update');
}
foreach (array_unique($files) as $file) {
    if (! is_file($root.'/'.$file) || filesize($root.'/'.$file) > 1000000 || str_contains($file, '..')) {
        throw new RuntimeException('Invalid update file: '.$file);
    }
    $zip->addFile($root.'/'.$file, $file);
}
$zip->close();
echo 'Map update created: '.count(array_unique($files))." files; no environment or database files.\n";
