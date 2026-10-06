<?php

namespace App\Support;

use Closure;
use Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Csv
{
    /**
     * Stream a UTF-8 CSV file without loading the full dataset into memory.
     *
     * @param  array<int, string>  $headings
     * @param  Closure(): Generator<int, array<int, mixed>>  $rows
     */
    public static function download(string $filename, array $headings, Closure $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows): void {
            $stream = fopen('php://output', 'wb');

            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, self::sanitize($headings));

            foreach ($rows() as $row) {
                fputcsv($stream, self::sanitize($row));
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, max-age=0',
        ]);
    }

    /**
     * Prevent spreadsheet applications from interpreting untrusted cells as formulas.
     *
     * @param  array<int, mixed>  $row
     * @return array<int, string>
     */
    public static function sanitize(array $row): array
    {
        return array_map(function (mixed $value): string {
            $value = (string) ($value ?? '');

            return preg_match('/^[=+\-@]/', $value) ? "'{$value}" : $value;
        }, $row);
    }
}
