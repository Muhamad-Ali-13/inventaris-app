<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;

class LogAktivitasController extends Controller
{
    public function index()
    {
        $log = LogAktivitas::with('user')->latest()->paginate(20);
        return view('log.index', compact('log'));
    }
}
