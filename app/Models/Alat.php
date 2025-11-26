<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model {
    protected $fillable = ['nama_alat', 'id_ruangan'];

    public function ruangan() {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function sensor() {
        return $this->hasMany(Sensor::class, 'id_alat');
    }
}



