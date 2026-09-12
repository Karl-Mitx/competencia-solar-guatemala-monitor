<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function resolve(Alert $alert)
    {
        if ($alert->status === 'active') {
            $alert->update(['status' => 'resolved', 'resolved_at' => now()]);
        }

        return back()->with('success', 'Alerta marcada como resuelta.');
    }
}
