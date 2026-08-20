<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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
        'status' => 'string',
    ];

    protected $attributes = [
        'status' => 'aktif',
        'sks' => 1,
    ];

    protected function lowercaseAttributes(): array
    {
        return ['kode', 'nama', 'kategori', 'status'];
    }

    protected function trimmedAttributes(): array
    {
        return ['deskripsi'];
    }

    protected function caseInsensitiveUniqueAttributes(): array
    {
        return ['kode', 'nama'];
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'aktif' ? 'Aktif' : 'Nonaktif';
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('nama', 'asc');
        });
    }
}
