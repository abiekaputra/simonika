<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AplikasiChartController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'statusData' => $this->totalsBy('status_pemakaian'),
            'jenisData' => $this->totalsBy('jenis'),
            'basisData' => $this->totalsBy('basis_aplikasi'),
            'pengembangData' => $this->totalsBy('pengembang'),
        ]);
    }

    private function totalsBy(string $column)
    {
        return Aplikasi::query()
            ->select($column, DB::raw('count(*) as total'))
            ->groupBy($column)
            ->get();
    }
}
