<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessLog extends Model
{
    use SoftDeletes;
    protected $table = 'process_log';
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public $timestamps = true;
    public $relatedModel = [
        'update' => [
            [ProcessParam::class, 'process_log_id', 'id']
        ],
        'delete' => [
            'kelompok_dosen'
        ]
    ];
    public function process_param()
    {
        return $this->hasOne(ProcessParam::class, 'process_log_id', 'id');
    }
    public function kelompok_dosen()
    {
        return $this->belongsTo(KelompokDosen::class, 'item_key', 'id')->where('process_item_id', 1);
    }
    public function jadwal()
    {
        return $this->belongsTo(KelompokDosen::class, 'item_key', 'id')->where('process_item_id', 2);
    }

    public function scopeUnfinished($query, $process_item)
    {
        return $query->where('status', '!=', 'SUCCESS')->where('status', '!=', 'FAILURE')->where('deleted_at', NULL)->where('process_item_id', $process_item);
    }
    public function process_item()
    {
        return $this->belongsTo(ProcessItem::class, 'process_item_id', 'id');
    }
}
