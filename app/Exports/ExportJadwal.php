<?php

namespace App\Exports;

use App\Jadwal;
use App\JadwalDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportJadwal implements FromCollection, WithHeadings
{
    private $id;
    private $jadwal_detail_model;
    private $columns;
    private $headers;
    function __construct($id)
    {
        $this->id = $id;
        $this->jadwal_detail_model = new JadwalDetail();
        $this->columns = $this->jadwal_detail_model->getTableColumns();
        $except = ['id', 'jadwal_id', 'created_at', 'updated_at', 'deleted_at'];
        $this->columns = collect($this->columns)->filter(function ($item) use ($except) {
            if (!in_array($item, $except)) {
                return $item;
            }
        });
        $this->headers = $this->columns->map(function ($item) {
            $item = preg_replace("/[^a-zA-Z0-9]+/", " ", $item);
            $item = ucwords($item);
            return $item;
        })->values()->toArray();
    }

    public function headings(): array
    {
        return $this->headers;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $jadwal_detail = Jadwal::find($this->id)->jadwal_detail;

        $jadwal_detail = $jadwal_detail->map(function ($item) {
            $item = collect($item->toArray());
            $item = $item->only($this->columns->toArray());
            return $item->toArray();
        });
        return $jadwal_detail;
    }
}
