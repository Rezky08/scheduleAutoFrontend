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
            [ProcessLogDetail::class, 'process_log_id', 'id'],
            [AlgenResultLog::class, 'process_log_id', 'id']
        ],
        'delete' => [
            'kelompok_dosen'
        ]
    ];
    public function kelompok_dosen()
    {
        return $this->belongsTo(KelompokDosen::class, 'item_key', 'id')->where('process_item_id', 1);
    }

    public function scopeUnfinished($query)
    {
        return $query->where('status', '!=', 'SUCCESS')->where('status', '!=', 'FAILURE')->where('deleted_at', NULL);
    }
    public function process_item()
    {
        return $this->belongsTo(ProcessItem::class, 'process_item_id', 'id');
    }
}
