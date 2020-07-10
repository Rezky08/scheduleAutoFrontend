<?php

namespace App\Listeners;

use App\Events\AlgenKelompokDosenEvent;
use App\Events\CheckAlgenResultEvent;
use App\KelompokDosen;
use App\ProcessLog;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AlgenKelompokDosenListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $process_log_model;
    private $kelompok_dosen_model;
    public function __construct()
    {
        $this->process_log_model = new ProcessLog();
        $this->kelompok_dosen_model = new KelompokDosen();
    }

    /**
     * Handle the event.
     *
     * @param  AlgenKelompokDosenEvent  $event
     * @return void
     */
    public function handle(AlgenKelompokDosenEvent $event)
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
                // insert kelompok dosen detail
                $data_insert = $res->result->data;
                $data_insert = collect($data_insert);
                $process_log->fitness = $res->result->fit_score;

                $rules = [];
                $rules['*.kelompok_dosen_id'] = ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'];
                $data_insert = $data_insert->map(function ($item, $index) use ($process_log, &$rules) {
                    $item->kelompok_dosen_id = $process_log->item_key;
                    $item->created_at = new \DateTime;
                    $item = collect($item)->toArray();
                    $rules[$index . '.kode_dosen'] = [
                        Rule::unique('kelompok_dosen_detail', 'kode_dosen')->where(function ($query) use ($item) {
                            return $query->where('kelompok_dosen_id', $item['kelompok_dosen_id'])->where('kode_matkul', $item['kode_matkul'])->where('kelompok', $item['kelompok'])->where('deleted_at', NULL);
                        })
                    ];
                    return $item;
                })->toArray();
                // validate
                $validator = Validator::make($data_insert, $rules);
                if ($validator->fails()) {
                    Log::warning($validator->errors()->first());
                    return true;
                }
                try {
                    $res = $this->kelompok_dosen_model->detail()->insert($data_insert);
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
