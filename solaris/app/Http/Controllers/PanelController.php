<?php

namespace App\Http\Controllers;

use App\Http\Requests\PanelRequest;
use App\Models\SolarPanel;

class PanelController extends Controller
{
    public function index()
    {
        return view('panels.index', ['panels' => SolarPanel::orderBy('brand')->paginate(12)]);
    }

    public function create()
    {
        return view('panels.create', ['panel' => new SolarPanel(['is_active' => true])]);
    }

    public function edit(SolarPanel $panel)
    {
        return view('panels.edit', compact('panel'));
    }

    public function store(PanelRequest $request)
    {
        SolarPanel::create($request->validated());

        return redirect()->route('panels.index')->with('success', 'Modelo de panel registrado.');
    }

    public function update(PanelRequest $request, SolarPanel $panel)
    {
        $panel->update($request->validated());

        return redirect()->route('panels.index')->with('success', 'Modelo actualizado. Las capacidades instaladas se recalculan automáticamente.');
    }
}
