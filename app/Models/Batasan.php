<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batasan extends Model
{
    use SoftDeletes;
    
    protected $table = 'batasan';

    protected $fillable = [
        'id_sensor',
        'suhu_min',
        'suhu_max',
        'kelembapan_min',
        'kelembapan_max'
    ];

    // Relasi ke sensor
    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }
}
