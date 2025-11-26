<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelembapan extends Model
{
    protected $table = 'kelembapan'; // Sesuaikan nama table
    
    protected $fillable = [
        'nilai_kelembapan',
        'id_sensor'
    ];
    
    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }
}