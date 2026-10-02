<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name', 'like', "%{$s}%")
                    ->orWhere('action', 'like', "%{$s}%")
                    ->orWhere('details', 'like', "%{$s}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $modules = AuditLog::select('module')->distinct()->pluck('module');

        return view('audit.index', compact('logs', 'modules'));
    }
}
