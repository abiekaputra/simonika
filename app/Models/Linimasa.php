<?php
/**
 * Summary of namespace App\Models
 * dalam isi ini nanti nya bisa menambahkan isi dari tabel tersebut bisa di otak atik tapi harus lihat isinya bagaimana dan apa yang harus diganti
 * setidaknya ganti yang penting saja 
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linimasa extends Model
{
    use HasFactory;
    protected $table = 'Linimasa';
    protected $fillabel = [
        'tanggal',
        'nama_proyek',
        'nama_pegawai',
        'status_proyek',
        'tenggat_waktu'
    ];

    //
    public function pegawai(){
        return $this->belongsTo(Pegawai::class);
    }
    // relasi ke model proyek 
    public function proyek(){
        return $this->belongsTo(Proyek::class);
    }
    public function scopeByStatus($query, $status){
        return $query-.where ('status_proyek', $status);
    }
    public function scopeToday($wuerry){
        return $query->whereDate('tenggat_waktu', now()->tooDateString());
    }
    //accsesor untuk status projek 
    public function getStatusProyekAttribute($value)
    {
        return ucfirst($value);
    }
    //mutator format tanngal 
    public function setTanggalAttribute($value){
        $this->attributes['tanggal'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
        
    }
}
