<?php

namespace App\Http\Controllers;

use App\Dosen;
use App\Hari;
use App\Jadwal;
use App\JadwalDetail;
use App\Matakuliah;
use App\Ruang;
use App\Sesi;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JadwalDetailController extends Controller
{
    private $breadcrumbs_helper;
    private $jadwal_model;
    private $jadwal_detail_model;
    private $mata_kuliah_model;
    private $dosen_model;
    private $ruang_model;
    private $sesi_model;
    private $hari_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->jadwal_model = new Jadwal();
        $this->jadwal_detail_model = new JadwalDetail();
        $this->mata_kuliah_model = new Matakuliah();
        $this->dosen_model = new Dosen();
        $this->ruang_model = new Ruang();
        $this->sesi_model = new Sesi();
        $this->hari_model = new Hari();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $jadwal = $this->jadwal_model->find($id);
        if ($request->searchbox) {
            $jadwal_detail = $this->search($jadwal->jadwal_detail(), $request->searchbox);
        } else {
            $jadwal_detail = $jadwal->jadwal_detail();
        }
        $jadwal_detail = $jadwal_detail->paginate(15);
        $jadwal_detail->appends($request->all())->render();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Jadwal Detail ' . $id,
            'breadcrumbs' => $breadcrumbs,
            'jadwal' => $jadwal,
            'jadwal_detail' => $jadwal_detail,
        ];

        echo "<script>var jadwal_detail = " . $data['jadwal_detail']->toJson() . "</script>";

        return view('jadwal_detail.list', $data);
    }

    public function search($model, $string = "")
    {
        $columns = collect($this->jadwal_detail_model->getTableColumns());
        $exclude = ['id', 'jadwal_id', 'deleted_at', 'updated_at'];
        $columns = $columns->filter(function ($item) use ($exclude) {
            if (!in_array($item, $exclude)) {
                return $item;
            }
        })->values()->toArray();
        $jadwal_detail = $model;
        $jadwal_detail = $jadwal_detail->where('id', 'like', '%' . $string . '%');
        foreach ($columns as $item) {
            $jadwal_detail = $jadwal_detail->orwhere($item, 'like', '%' . $string . '%');
        }
        return $jadwal_detail;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $data_validation = [
            'jadwal_id' => $id,
        ];
        $rules = [
            'jadwal_id' => ['required', 'exists:jadwal,id,deleted_at,NULL']
        ];
        $validator = Validator::make($data_validation, $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }

        $jadwal = $this->jadwal_model->find($id);

        $mata_kuliah = $this->mata_kuliah_model->with('dosen_matkul')->get();
        $dosen = $this->dosen_model->all();
        $ruang = $this->ruang_model->all();
        $sesi = $this->sesi_model->all();
        $hari = $this->hari_model->all();
        $dosen_mata_kuliah = $mata_kuliah->mapWithKeys(function ($item) {
            $dosen = [];
            foreach ($item->dosen_matkul as $key => $value) {
                $dosen[] = $value->dosen->toArray();
            }
            return [$item->kode_matkul => $dosen];
        });

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Ubah Kelompok Dosen Detail',
            'breadcrumbs' => $breadcrumbs,
            'jadwal' => $jadwal,
            'mata_kuliah' => $mata_kuliah,
            'ruang' => $ruang,
            'sesi' => $sesi,
            'hari' => $hari,
            'dosen' => $dosen,
            'action' => URL::to('/penjadwalan/jadwal/detail/' . $id . '/tambah')
        ];

        echo "<script>var dosen_mata_kuliah = " . $dosen_mata_kuliah->toJson() . "</script>";

        return view('jadwal_detail.form', $data);
    }

    public function updatePelanggaran(Request $request, $id)
    {
        $rules = [
            'jadwal_detail.*.kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'jadwal_detail.*.id' => ['required', 'exists:jadwal_detail,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $jadwal_detail_request = collect($request->jadwal_detail);
        $jadwal_detail = $this->jadwal_model->find($id)->detail->whereIn('id', $jadwal_detail_request->pluck('id')->toArray());
        try {
            $jadwal_detail->map(function ($item, $index) use ($jadwal_detail_request) {
                $update = $jadwal_detail_request->where('id', $item->id)->first();
                $item->kode_dosen = $update['kode_dosen'];
                $item->save();
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $message = [
            'success' => 'Berhasil menyimpan data!'
        ];
        return redirect()->back()->with($message);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id, $iddetail)
    {
        $rules = [
            'id_jadwal' => ['required', 'exists:jadwal,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kelompok' => ['required', 'alpha'],
            'kapasitas' => ['required', 'integer'],
            'ruang' => ['required', 'exists:ruang,id,deleted_at,NULL'],
            'sesi' => ['required', 'exists:sesi,id,deleted_at,NULL'],
            'hari' => ['required', 'exists:hari,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $mata_kuliah = $this->mata_kuliah_model->where('kode_matkul', $request->kode_matkul)->first();
        $dosen = $this->dosen_model->where('kode_dosen', $request->kode_dosen)->first();
        $ruang = $this->ruang_model->find($request->ruang);
        $hari = $this->hari_model->find($request->hari);
        $sesi = $this->sesi_model->find($request->sesi);
        $sesi_all = $this->sesi_model->all();

        $rules = [
            'all_input' => [Rule::unique('jadwal_detail')->where(function ($query) use ($mata_kuliah, $dosen, $hari, $ruang, $sesi, $id) {
                return $query->where('jadwal_id', $id)->where('kode_matkul', $mata_kuliah->kode_matkul)->where('kode_dosen', $dosen->kode_dosen)->where('hari', $hari->nama_hari)->where('ruang', $ruang->nama_ruang)->where('sesi', $sesi->sesi_mulai)->where('deleted_at', NULL);
            })]
        ];
        $validator = Validator::make([], $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $sesi_all = $sesi_all->sortBy('sesi_mulai');
        $sesi_current_index = $sesi_all->where('sesi_mulai', $sesi->sesi_mulai)->keys()->first();
        $sesi_next_index = $sesi_current_index + $mata_kuliah->sks_matkul - 1;
        try {
            $sesi_next = $sesi_all[$sesi_next_index];
        } catch (Exception $e) {
            $message = [
                'error' => "Sesi melewati batas"
            ];
            return redirect()->back()->with($message)->withInput();
        }

        $sesi = $sesi->toArray();
        $sesi_next = $sesi_next->toArray();
        $sesi['sesi_selesai'] = $sesi_next['sesi_selesai'];
        $mata_kuliah = $mata_kuliah->toArray();
        $dosen = $dosen->toArray();

        $data_insert = [
            'jadwal_id' => $request->id_jadwal,
            'kelompok' => $request->kelompok,
            'kapasitas' => $request->kapasitas,
            'created_at' => new \DateTime,
            'hari' => $hari->nama_hari,
            'ruang' => $ruang->nama_ruang
        ];
        $data_insert = collect($data_insert)->merge($mata_kuliah)->merge($dosen)->merge($sesi);
        $columns = $this->jadwal_detail_model->getTableColumns();
        $columns = collect($columns)->filter(function ($item) {
            if ($item != 'id') {
                return $item;
            }
        })->toArray();
        $data_insert = $data_insert->only($columns)->toArray();

        try {
            $status = $this->jadwal_detail_model->insert($data_insert);
            if ($status) {
                $message = [
                    'success' => "Jadwal detail berhasil ditambahkan!"
                ];
                return redirect(URL::to('/penjadwalan/jadwal/detail/' . $id))->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            Log::error($e->getMessage());
            return redirect(URL::to('/penjadwalan/jadwal/detail/' . $id . '/tambah'))->with($message)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $iddetail)
    {
        $data_validation = [
            'jadwal_id' => $id,
        ];
        $rules = [
            'jadwal_id' => ['required', 'exists:jadwal,id,deleted_at,NULL']
        ];
        $validator = Validator::make($data_validation, $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }

        $jadwal = $this->jadwal_model->find($id);

        $mata_kuliah = $this->mata_kuliah_model->with('dosen_matkul')->get();
        $dosen = $this->dosen_model->all();
        $ruang = $this->ruang_model->all();
        $sesi = $this->sesi_model->all();
        $hari = $this->hari_model->all();
        $dosen_mata_kuliah = $mata_kuliah->mapWithKeys(function ($item) {
            $dosen = [];
            foreach ($item->dosen_matkul as $key => $value) {
                $dosen[] = $value->dosen->toArray();
            }
            return [$item->kode_matkul => $dosen];
        });

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Ubah Kelompok Dosen Detail',
            'breadcrumbs' => $breadcrumbs,
            'jadwal' => $jadwal,
            'jadwal_detail' => $jadwal->jadwal_detail->find($iddetail),
            'mata_kuliah' => $mata_kuliah,
            'ruang' => $ruang,
            'sesi' => $sesi,
            'hari' => $hari,
            'dosen' => $dosen,
            'action' => URL::to('/penjadwalan/jadwal/detail/' . $id . '/ubah/' . $iddetail)
        ];

        echo "<script>var dosen_mata_kuliah = " . $dosen_mata_kuliah->toJson() . "</script>";

        return view('jadwal_detail.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $iddetail)
    {
        $rules = [
            'id_jadwal' => ['required', 'exists:jadwal,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kelompok' => ['required', 'alpha'],
            'kapasitas' => ['required', 'integer'],
            'ruang' => ['required', 'exists:ruang,id,deleted_at,NULL'],
            'sesi' => ['required', 'exists:sesi,id,deleted_at,NULL'],
            'hari' => ['required', 'exists:hari,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $mata_kuliah = $this->mata_kuliah_model->where('kode_matkul', $request->kode_matkul)->first();
        $dosen = $this->dosen_model->where('kode_dosen', $request->kode_dosen)->first();
        $ruang = $this->ruang_model->find($request->ruang);
        $hari = $this->hari_model->find($request->hari);
        $sesi = $this->sesi_model->find($request->sesi);
        $sesi_all = $this->sesi_model->all();

        $rules = [
            'all_input' => [Rule::unique('jadwal_detail')->where(function ($query) use ($mata_kuliah, $dosen, $hari, $ruang, $sesi, $iddetail, $id) {
                return $query->where('jadwal_id', $id)->where('kode_matkul', $mata_kuliah->kode_matkul)->where('kode_dosen', $dosen->kode_dosen)->where('hari', $hari->nama_hari)->where('ruang', $ruang->nama_ruang)->where('sesi', $sesi->sesi_mulai)->where('deleted_at', NULL)->where('id', '!=', $iddetail);
            })]
        ];
        $validator = Validator::make([], $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $sesi_all = $sesi_all->sortBy('sesi_mulai');
        $sesi_current_index = $sesi_all->where('sesi_mulai', $sesi->sesi_mulai)->keys()->first();
        $sesi_next_index = $sesi_current_index + $mata_kuliah->sks_matkul - 1;
        try {
            $sesi_next = $sesi_all[$sesi_next_index];
        } catch (Exception $e) {
            $message = [
                'error' => "Sesi melewati batas"
            ];
            return redirect()->back()->with($message)->withInput();
        }

        $sesi = $sesi->toArray();
        $sesi_next = $sesi_next->toArray();
        $sesi['sesi_selesai'] = $sesi_next['sesi_selesai'];
        $mata_kuliah = $mata_kuliah->toArray();
        $dosen = $dosen->toArray();

        $data_update = [
            'kelompok' => $request->kelompok,
            'kapasitas' => $request->kapasitas,
            'hari' => $hari->nama_hari,
            'ruang' => $ruang->nama_ruang
        ];
        $data_update = collect($data_update)->merge($mata_kuliah)->merge($dosen)->merge($sesi);
        $columns = $this->jadwal_detail_model->getTableColumns();
        $columns = collect($columns)->filter(function ($item) {
            if ($item != 'id') {
                return $item;
            }
        })->toArray();
        $data_update = $data_update->only($columns);

        try {
            $jadwal_detail = $this->jadwal_detail_model->find($iddetail);
            $data_update->map(function ($item, $key) use (&$jadwal_detail) {
                $jadwal_detail->$key = $item;
            });

            $status = $jadwal_detail->save();
            if ($status) {
                $message = [
                    'success' => "Jadwal detail berhasil diubah!"
                ];
                return redirect(URL::to('/penjadwalan/jadwal/detail/' . $id))->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            Log::error($e->getMessage());
            return redirect(URL::to('/penjadwalan/jadwal/detail/' . $id . '/ubah/' . $iddetail))->with($message)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $idDetail)
    {
        $jadwal = $this->jadwal_model->find($id);
        if (!$jadwal) {
            $message = [
                'error' => "<strong>Jadwal Tidak Ditemukan!</strong>"
            ];
            return redirect('/penjadwalan/jadwal/')->with($message);
        }

        $jadwal_detail = $jadwal->jadwal_detail->find($idDetail);
        if (!$jadwal_detail) {
            $message = [
                'error' => "<strong>Jadwal Detail Tidak Ditemukan!</strong>"
            ];
            return redirect('/penjadwalan/jadwal/detail/' . $id)->with($message);
        }

        $status = $jadwal_detail->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Jadwal Detail!'</strong>" . 'Jadwal Detail ' . $jadwal_detail->getOriginal('nama_dosen') . ' telah dihapus.'
            ];
            return redirect('/penjadwalan/jadwal/detail/' . $id)->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Jadwal Detail!'
            ];
            return redirect('/penjadwalan/jadwal/detail/' . $id)->with($message);
        }
    }
}
