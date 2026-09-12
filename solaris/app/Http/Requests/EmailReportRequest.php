<?php

namespace App\Http\Requests;

class EmailReportRequest extends FilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), ['email' => ['required', 'email:rfc', 'max:254']]);
    }
}
