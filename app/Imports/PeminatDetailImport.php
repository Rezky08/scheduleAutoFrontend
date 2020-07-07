<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PeminatDetailImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            PeminatDetailImport::create([
                'kode_matkul' => $row[0],
                'jumlah_peminat' => $row[1],
            ]);
        }
    }
}
