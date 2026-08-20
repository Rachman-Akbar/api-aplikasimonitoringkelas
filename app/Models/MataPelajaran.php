<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'sks',
        'kategori',
        'status',
    ];

    protected $casts = [
        'sks' => 'integer',
        'status' => 'string', // This should be 'string' since it's an enum in the database
    ];

    protected $attributes = [
        'status' => 'aktif',
        'sks' => 1,
    ];

    // Relasi: Jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'aktif' ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Boot the model and add a global scope to order by nama by default
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('nama', 'asc');
        });
    }
}
