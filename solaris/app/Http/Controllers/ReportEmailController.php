<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailReportRequest;
use App\Mail\DepartmentReport;
use App\Services\AnalyticsService;
use App\Services\ReportCsv;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ReportEmailController extends Controller
{
    public function __invoke(EmailReportRequest $request, AnalyticsService $analytics, ReportCsv $csv)
    {
        $data = $request->validated();
        $email = $data['email'];
        unset($data['email']);
        $filters = array_filter($data, fn ($value) => $value !== null && $value !== '');
        $redirect = fn () => redirect()->route('reports', $filters);
        if (! config('mail.reports_enabled') || config('mail.default') !== 'smtp') {
            return $redirect()->with('error', 'El envío por correo todavía no está disponible. Puedes descargar el CSV.');
        }
        $recipientKey = 'report-email:'.hash('sha256', strtolower($email));
        if (RateLimiter::tooManyAttempts($recipientKey, 2) || RateLimiter::tooManyAttempts('report-email:global', 50)) {
            return $redirect()->with('error', 'Se alcanzó el límite de envíos. Intenta más tarde o descarga el CSV.');
        }
        RateLimiter::hit($recipientKey, 3600);
        RateLimiter::hit('report-email:global', 3600);
        try {
            $rows = $analytics->dashboard($filters)['ranking'];
            Mail::to($email)->send(new DepartmentReport($csv->content($rows), $csv->filename(), $filters));
        } catch (\Throwable $exception) {
            // Do not log SMTP exceptions: they may include credentials or recipient details.
            return $redirect()->with('error', 'No se pudo enviar el correo. Intenta más tarde o descarga el CSV.');
        }

        return $redirect()->with('success', 'El reporte fue aceptado por el servicio de correo. Revisa tu bandeja de entrada y spam.');
    }
}
