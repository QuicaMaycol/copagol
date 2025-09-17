<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Jugador;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function systemLogs(Request $request)
    {
        $logs = SystemLog::with(['user', 'subject'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.system-logs.index', compact('logs'));
    }

    public function campeonatoAudits(Campeonato $campeonato)
    {
        $audits = $campeonato->audits()->with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.audits.campeonato', compact('campeonato', 'audits'));
    }

    public function jugadorAudits(Jugador $jugador)
    {
        $audits = $jugador->audits()->with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.audits.jugador', compact('jugador', 'audits'));
    }
}
