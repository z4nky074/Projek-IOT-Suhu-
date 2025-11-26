<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suhu extends Model
{
    protected $table = 'suhu'; // Sesuaikan nama table
    
    protected $fillable = [
        'nilai_suhu',
        'id_sensor'
    ];
    
    // Relasi ke Sensor (jika ada)
    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }
}