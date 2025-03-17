<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyeks';

    protected $fillable = ['nama_proyek', 'kategori_id', 'deskripsi'];

    public function linimasa()
    {
        return $this->hasMany(Linimasa::class);
    }
    
    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_proyek');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
