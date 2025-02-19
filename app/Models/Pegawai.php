<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    // kolom yang dapat disi dalam model pegawai adalah ini 
    protected $fillable = ['nama', 'user_id'];
    //relasi nya adalah 
    public function user(){
        return $this->belongsTo(User::class);
    }
    // relasi ke model lini masa
    public function linimasa(){return $this->hasMany(Linimasa::class);}
    
    //relasi proyek aktif 
    public function proyekAktif(){
        return $this->linimasa()->where('status', 'aktif');
    }
    //menghitung jumlah proyek
    public function jumlahProyek(){
        return $this->linimasa()->count();
    }
}
