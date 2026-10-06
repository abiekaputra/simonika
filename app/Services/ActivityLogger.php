<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public function record(string $module, string $activity, string $type, string $detail, ?int $userId = null): void
    {
        LogAktivitas::create([
            'user_id' => $userId ?? Auth::id(),
            'aktivitas' => $activity,
            'tipe_aktivitas' => $type,
            'modul' => $module,
            'detail' => $detail,
        ]);
    }
}
