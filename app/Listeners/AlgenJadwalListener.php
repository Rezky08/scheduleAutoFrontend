<?php

namespace App\Listeners;

use App\Events\AlgenJadwalEvent;
use App\Events\CheckAlgenResultEvent;
use App\Jadwal;
use App\JadwalDetail;
use App\ProcessLog;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AlgenJadwalListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $jadwal_model;
    private $process_log_model;
    private $jadwal_detail_model;
    private $jadwal_detail_columns;
    public function __construct()
    {
        $this->jadwal_model = new Jadwal();
        $this->process_log_model = new ProcessLog();
        $this->jadwal_detail_model = new JadwalDetail();
        $this->jadwal_detail_columns = collect($this->jadwal_detail_model->getTableColumns());
        $columns = ['id', 'updated_at', 'deleted_at'];
        $this->jadwal_detail_columns = $this->jadwal_detail_columns->filter(function ($item) use ($columns) {
            if (!in_array($item, $columns)) {
                return $item;
            }
        })->values()->toArray();
    }

    /**
     * Handle the event.
     *
     * @param  AlgenJadwalEvent  $event
     * @return void
     */
    public function handle(AlgenJadwalEvent $event)
    {
        $process_log = $this->process_log_model->find($event->process_id);
        if (!$process_log) {
            Log::warning('Process ' . $event->process_id . ' Tidak Ditemukan');
            return false;
        }

        $res = event(new CheckAlgenResultEvent($event->url_python, $process_log->celery_id));
        $res = collect($res)->first();
        if (!$res) {
            return false;
        }
        $status = $res->status;
        if ($status == "FAILURE" || $status == "SUCCESS") {
            $process_log->status = $status;
            $process_log->updated_at = new \DateTime;
            $process_log->attempt += 1;


            if ($status == "SUCCESS") {
                // insert jadwal
                $data_insert = $res->result->data;
                $data_insert = collect($data_insert);
                $process_log->fitness = $res->result->fit_score;
                $rules = [];
                $data_insert = $data_insert->map(function ($item, $index) use ($process_log, &$rules) {
                    $item->jadwal_id = $process_log->item_key;
                    $item->created_at = new \DateTime;
                    $item = collect($item)->toArray();
                    $item = collect($item);
                    $item = $item->only($this->jadwal_detail_columns)->toArray();
                    $rules[$index . '.data'] = [
                        Rule::unique('jadwal_detail')->where(function ($query) use ($item) {
                            foreach ($item as $key => $value) {
                                $query = $query->where($key, $value);
                            }
                            return $query;
                        })
                    ];
                    return $item;
                })->toArray();

                // validate
                $validator = Validator::make($data_insert, $rules);
                if ($validator->fails()) {
                    $message = $validator->errors()->first();
                    Log::warning($message);
                    return true;
                }

                try {
                    $res = $this->jadwal_model->jadwal_detail()->insert($data_insert);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return true;
                }
            }
            $process_log->save();
            return true;
        }
        if ($process_log->created_at->diffInHours() > $event->expired) {
            $process_log->forceDelete();
        } else {
            $process_log->status = $status;
            $process_log->updated_at = new \DateTime;
            $process_log->attempt += 1;
            $process_log->save();
        }
        return false;
    }
}
