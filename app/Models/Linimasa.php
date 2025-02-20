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

    protected $table = 'linimasa';

    protected $fillable = [
        'tanggal',
        'nama_proyek',
        'nama_pegawai',
        'status_proyek',
        'tenggat_waktu',
    ];
}

