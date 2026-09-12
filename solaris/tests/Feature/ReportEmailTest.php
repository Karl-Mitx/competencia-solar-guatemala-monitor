<?php

namespace Tests\Feature;

use App\Mail\DepartmentReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReportEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_email_attaches_the_same_csv_as_download(): void
    {
        Mail::fake();
        config(['mail.reports_enabled' => true, 'mail.default' => 'smtp']);
        $filters = ['from' => '2026-01', 'to' => '2026-02'];
        $download = $this->get('/reports/export?'.http_build_query($filters))->assertOk()->streamedContent();
        $this->post('/reports/email', [...$filters, 'email' => 'reader@example.com'])
            ->assertRedirect(route('reports', $filters))->assertSessionHas('success');
        Mail::assertSent(DepartmentReport::class, function ($mail) use ($download) {
            $mail->build();
            return $mail->hasTo('reader@example.com') && $mail->csv === $download && count($mail->rawAttachments) === 1;
        });
    }

    public function test_unconfigured_mail_never_claims_to_send(): void
    {
        Mail::fake();
        config(['mail.reports_enabled' => true, 'mail.default' => 'log']);
        $this->post('/reports/email', ['email' => 'other@example.com'])->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    public function test_invalid_email_and_period_are_rejected(): void
    {
        Mail::fake();
        $this->post('/reports/email', ['email' => 'invalid', 'from' => '2026-02', 'to' => '2026-01'])
            ->assertSessionHasErrors(['email', 'to']);
        Mail::assertNothingSent();
    }

    public function test_recipient_limit_prevents_further_sends(): void
    {
        Mail::fake();
        config(['mail.reports_enabled' => true, 'mail.default' => 'smtp']);
        $key = 'report-email:'.hash('sha256', 'limited@example.com');
        \Illuminate\Support\Facades\RateLimiter::hit($key, 3600);
        \Illuminate\Support\Facades\RateLimiter::hit($key, 3600);
        $this->post('/reports/email', ['email' => 'limited@example.com'])->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    public function test_transport_failure_is_reported_without_sensitive_details(): void
    {
        config(['mail.reports_enabled' => true, 'mail.default' => 'smtp']);
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('private transport detail'));
        $this->post('/reports/email', ['email' => 'failure@example.com'])->assertSessionHas('error', 'No se pudo enviar el correo. Intenta más tarde o descarga el CSV.');
    }
}
