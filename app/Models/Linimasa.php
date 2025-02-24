<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Linimasa extends Model
{
    use HasFactory;

    protected $table = 'linimasa'; // Nama tabel di database
    protected $fillable = ['nama_pegawai', 'nama_proyek', 'tanggal', 'tenggat_waktu', 'tanggal_selesai']; // Kolom yang bisa diisi

    // Accessor untuk menghitung status proyek secara otomatis
    public function getStatusProyekAttribute()
    {
        $today = now();
        $mulai = Carbon::parse($this->tanggal);
        $tenggat = Carbon::parse($this->tenggat_waktu);
        $tanggalSelesai = $this->tanggal_selesai ? Carbon::parse($this->tanggal_selesai) : null;

        switch (true) {
            case $tanggalSelesai !== null:
                return $tanggalSelesai > $tenggat ? 'Selesai (Terlambat)' : 'Selesai';
            case $today > $tenggat:
                return 'Terlambat';
            case $today >= $mulai:
                return 'Berjalan';
            default:
                return 'Belum Dimulai';
        }
    }
}
