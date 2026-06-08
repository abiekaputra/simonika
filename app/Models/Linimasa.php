<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linimasa extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'proyek_id',
        'status_proyek',
        'mulai',
        'tenggat',
        'tanggal_selesai',
        'deskripsi',
        'status_manual',
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
