<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    
    protected $fillable = ['nama_ruangan']; // Sesuaikan field
    
    public function alat()
    {
        return $this->hasMany(Alat::class, 'id_ruangan');
    }
}