<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linimasa extends Model
{
    use HasFactory;

    protected $fillable = ['pegawai_id', 'proyek_id', 'status_proyek', 'mulai', 'tenggat', 'deskripsi'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
    public function kategori()
{
    return $this->belongsTo(Kategori::class, 'kategori_id');
}

}
