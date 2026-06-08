<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogAktivitasController extends Controller
{
    public function export(): StreamedResponse
    {
        $logs = LogAktivitas::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Timestamp');
        $sheet->setCellValue('B1', 'Admin');
        $sheet->setCellValue('C1', 'Activity');
        $sheet->setCellValue('D1', 'Module');
        $sheet->setCellValue('E1', 'Detail');

        $row = 2;
        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $log->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB');
            $sheet->setCellValue('B' . $row, optional($log->user)->nama ?? 'Unknown');
            $sheet->setCellValue('C' . $row, $log->aktivitas);
            $sheet->setCellValue('D' . $row, $log->modul);
            $sheet->setCellValue('E' . $row, $log->detail);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'activity_log_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
