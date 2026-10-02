<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'min_notice_days' => 'required|integer|min:0',
            'organization_name' => 'required|string|max:255',
            'unit_name' => 'required|string|max:255',
            'office_address' => 'required|string|max:500',
            'upf_phone' => 'required|string|max:50',
            'upf_email' => 'required|email|max:255',
            'allow_conflict_override' => 'nullable|boolean',
        ]);

        Setting::set('min_notice_days', $validated['min_notice_days'], 'Tempoh notis minimum permohonan (hari)');
        Setting::set('organization_name', $validated['organization_name'], 'Nama Agensi / Organisasi');
        Setting::set('unit_name', $validated['unit_name'], 'Nama Unit Pengendali');
        Setting::set('office_address', $validated['office_address'], 'Alamat Pejabat');
        Setting::set('upf_phone', $validated['upf_phone'], 'No. Telefon UPF');
        Setting::set('upf_email', $validated['upf_email'], 'Emel UPF');
        Setting::set('allow_conflict_override', $request->boolean('allow_conflict_override') ? '1' : '0', 'Membenarkan override penugasan bertindih');

        AuditLog::record('Kemaskini Tetapan', 'Tetapan', 'Mengemaskini tetapan sistem UPF.');

        return redirect()->route('settings.index')->with('success', 'Tetapan sistem berjaya disimpan.');
    }
}
