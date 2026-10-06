<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Support\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogAktivitasController extends Controller
{
    public function export(): StreamedResponse
    {
        $filename = 'activity_log_'.now()->format('Y-m-d_His').'.csv';

        return Csv::download($filename, [
            'Timestamp',
            'Admin',
            'Activity',
            'Module',
            'Detail',
        ], function () {
            foreach (LogAktivitas::with('user')->latest()->cursor() as $log) {
                yield [
                    $log->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB',
                    optional($log->user)->nama ?? 'Unknown',
                    $log->aktivitas,
                    $log->modul,
                    $log->detail,
                ];
            }
        });
    }
}
