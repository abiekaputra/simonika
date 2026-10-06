<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linimasa extends Model
{
    use HasFactory;

    public const STATUSES = [
        'Selesai Lebih Cepat',
        'Tepat Waktu',
        'Terlambat',
        'Revisi',
        'Proses',
        'To Do Next',
    ];

    protected $fillable = [
        'pegawai_id',
        'proyek_id',
        'status_proyek',
        'mulai',
        'tenggat',
        'tanggal_selesai',
        'deskripsi',
    ];

    protected $casts = [
        'mulai' => 'date',
        'tenggat' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}
