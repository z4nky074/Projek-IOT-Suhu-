<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $table = 'sensor';
    
    protected $fillable = [
        'nama_sensor',
        'id_alat'
    ];
    
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'id_alat');
    }
    
    public function suhu()
    {
        return $this->hasMany(Suhu::class, 'id_sensor');
    }
    
    public function kelembapan()
    {
        return $this->hasMany(Kelembapan::class, 'id_sensor');
    }
}