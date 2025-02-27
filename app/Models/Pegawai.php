<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai'; // Nama tabel
    protected $fillable = ['nama', 'no_telepon', 'email']; // Kolom yang dapat diisi

    // Relasi ke User
    public function user(){
        return $this->belongsTo(User::class); // Tabel pegawai harus punya kolom user_id
    }

    // Relasi ke Linimasa
    public function linimasa(){
        return $this->hasMany(Linimasa::class); // Tabel linimasa harus punya kolom pegawai_id
    }

    // Proyek Aktif
    public function proyekAktif(){
        return $this->linimasa()->where('status', 'aktif');
    }

    // Menghitung jumlah proyek
    public function jumlahProyek(){
        return $this->linimasa()->count();
    }

    // Scope untuk Pegawai dengan Proyek Aktif
    public function scopeAktif($query){
        return $query->whereHas('linimasa', function ($query) {
            $query->where('status', 'aktif');
        });
    }

    // Accessor untuk Nama
    public function getNamaAttribute($value){
        return ucfirst($value);
    }
}
