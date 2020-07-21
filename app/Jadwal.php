<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use SoftDeletes;
    protected $table = 'jadwal';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $relatedModel = [
        'update' => [
            [JadwalDetail::class, 'jadwal_id', 'id']
        ],
        'delete' => [
            'process_log'
        ]
    ];

    public function jadwal_detail()
    {
        return $this->hasMany(JadwalDetail::class, 'jadwal_id', 'id');
    }
    public function semester_detail()
    {
        return $this->belongsTo(SemesterDetail::class, 'semester', 'semester');
    }
    public function process_log()
    {
        return $this->hasOne(ProcessLog::class, 'item_key', 'id')->where('process_log.process_item_id', 2);
    }
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
