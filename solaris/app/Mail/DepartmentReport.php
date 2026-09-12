<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class DepartmentReport extends Mailable
{
    public function __construct(public string $csv, public string $filename, public array $filters) {}

    public function build(): static
    {
        return $this->subject('Tu reporte de energía solar · SOLARIS Guatemala')
            ->view('emails.department-report')
            ->attachData($this->csv, $this->filename, ['mime' => 'text/csv']);
    }
}
