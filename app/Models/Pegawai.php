<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';

    protected $fillable = ['nama', 'nomor_telepon', 'email'];

    public function linimasa()
    {
        return $this->hasMany(Linimasa::class);
    }

    public function proyek()
    {
        return $this->belongsToMany(Proyek::class, 'pegawai_proyek');
    }
}
