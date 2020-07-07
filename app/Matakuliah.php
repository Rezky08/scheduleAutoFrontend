<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\True_;

class Matakuliah extends Model
{
    use SoftDeletes;
    protected $table = 'mata_kuliah';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $relatedModel = [
        'update' => [
            [DosenMatakuliah::class, 'kode_matkul'],
            [PeminatDetail::class, 'kode_matkul'],
            [KelompokDosenDetail::class, 'kode_matkul']
        ],
        'delete' => [
            'dosen_matkul', 'peminat', 'kelompok_dosen'
        ]
    ];

    public function scopeStatus($query, $status = True)
    {
        $query->where('status_matkul', $status);
    }

    public function dosen_matkul()
    {
        return $this->hasMany(DosenMatakuliah::class, 'kode_matkul', 'kode_matkul');
    }
    public function peminat()
    {
        return $this->hasMany(PeminatDetail::class, 'kode_matkul', 'kode_matkul');
    }
    public function kelompok_dosen()
    {
        return $this->hasMany(KelompokDosenDetail::class, 'kode_matkul', 'kode_matkul');
    }
    public function program_studi()
    {
        return $this->belongsTo(ProgramStudi::class, 'kode_prodi', 'kode_prodi');
    }
}
