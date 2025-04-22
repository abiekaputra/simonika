<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendataan extends Model
{
    use HasFactory;

    protected $table = 'pendataans';
    
    protected $fillable = [
        'universitas',
        'jumlah_orang',
        'tanggal_masuk',
        'tanggal_keluar'
    ];
}
