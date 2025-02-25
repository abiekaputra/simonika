<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Linimasa extends Model
{
    use HasFactory;

    protected $table = 'linimasa'; // Nama tabel di database
    protected $fillable = [
        'nama_pegawai',
        'nama_proyek',
        'tanggal',
        'tenggat_waktu',
        'tanggal_selesai',
        'status_manual', // Tambahkan ini
    ];
    

   
}
