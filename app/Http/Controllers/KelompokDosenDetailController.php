<?php

namespace App\Http\Controllers;

use App\Dosen;
use App\KelompokDosen;
use App\Matakuliah;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class KelompokDosenDetailController extends Controller
{
    private $breadcrumbs_helper;
    private $kelompok_dosen_model;
    private $mata_kuliah_model;
    private $dosen_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->kelompok_dosen_model = new KelompokDosen();
        $this->mata_kuliah_model = new Matakuliah();
        $this->dosen_model = new Dosen();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $kelompok_dosen = $this->kelompok_dosen_model->find($id);
        if ($request->searchbox) {
            $kelompok_dosen_detail = $this->search($kelompok_dosen->detail(), $request->searchbox);
        } else {
            $kelompok_dosen_detail = $kelompok_dosen->detail();
        }
        $kelompok_dosen_detail_all = $kelompok_dosen_detail->get();
        $kelompok_dosen_detail = $kelompok_dosen_detail->paginate(15);
        $kelompok_dosen_detail->appends($request->all())->render();
        $kelompok_dosen_count = $kelompok_dosen->detail()->countMengajar()->get();

        $mata_kuliah = $this->mata_kuliah_model->all();
        $dosen = $this->dosen_model->all();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Kelompok Dosen Detail ' . $id,
            'breadcrumbs' => $breadcrumbs,
            'kelompok_dosen' => $kelompok_dosen,
            'kelompok_dosen_detail' => $kelompok_dosen_detail,
            'kelompok_dosen_detail_all' => $kelompok_dosen_detail_all,
            'kelompok_dosen_count' => $kelompok_dosen_count,
            'mata_kuliah' => $mata_kuliah,
            'dosen' => $dosen,
            'action' => URL::to('/penjadwalan/kelompok-dosen/detail/' . $id . '/pelanggaran/simpan'),
            'pelanggaran' => []
        ];

        try {
            $parameters = $kelompok_dosen->process_log->process_param->parameters;
            $parameters = json_decode($parameters);
            $pelanggaran = $kelompok_dosen_count->filter(function ($item, $index) use ($parameters) {
                if ($item->count > $parameters->rules->max_kelompok) {
                    return $item;
                }
            })->values();
            $data['pelanggaran'] = $pelanggaran;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        echo "<script>var kelompok_dosen_detail = " . $data['kelompok_dosen_detail']->toJson() . "</script>";

        return view('kelompok_dosen_detail.list', $data);
    }

    public function search($model, $string = "")
    {
        $model->leftJoin('mata_kuliah', 'mata_kuliah.kode_matkul', '=', 'kelompok_dosen_detail.kode_matkul');
        $model->leftJoin('dosen', 'dosen.kode_dosen', '=', 'kelompok_dosen_detail.kode_dosen');
        $kelompok_dosen_detail = $model->where('kelompok_dosen_detail.kode_matkul', 'like', '%' . $string . '%')->orwhere('kelompok_dosen_detail.kode_dosen', 'like', '%' . $string . '%')->orwhere('mata_kuliah.nama_matkul', 'like', '%' . $string . '%')->orwhere('dosen.nama_dosen', 'like', '%' . $string . '%');
        return $kelompok_dosen_detail;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {


        $data_validation = [
            'kelompok_dosen_id' => $id,
        ];
        $rules = [
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL']
        ];
        $validator = Validator::make($data_validation, $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect('/penjadwalan/kelompok-dosen')->with($message);
        }

        $kelompok_dosen = $this->kelompok_dosen_model->find($id);

        $mata_kuliah = $this->mata_kuliah_model->all();
        $dosen = $this->dosen_model->all();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Ubah Kelompok Dosen Detail',
            'breadcrumbs' => $breadcrumbs,
            'kelompok_dosen' => $kelompok_dosen,
            'mata_kuliah' => $mata_kuliah,
            'dosen' => $dosen,
            'action' => URL::to('/penjadwalan/kelompok-dosen/detail/' . $id . '/tambah')
        ];
        return view('kelompok_dosen_detail.form', $data);
    }

    public function updatePelanggaran(Request $request, $id)
    {
        $rules = [
            'kelompok_dosen_detail.*.kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kelompok_dosen_detail.*.id' => ['required', 'exists:kelompok_dosen_detail,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $kelompok_dosen_detail_request = collect($request->kelompok_dosen_detail);
        $kelompok_dosen_detail = $this->kelompok_dosen_model->find($id)->detail->whereIn('id', $kelompok_dosen_detail_request->pluck('id')->toArray());
        try {
            $kelompok_dosen_detail->map(function ($item, $index) use ($kelompok_dosen_detail_request) {
                $update = $kelompok_dosen_detail_request->where('id', $item->id)->first();
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
    public function store(Request $request)
    {
        dd($request->all());
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
    public function edit(Request $request, $idKelompokDosen, $id)
    {
        $kelompok_dosen = $this->kelompok_dosen_model->find($idKelompokDosen);
        $kelompok_dosen_detail = $kelompok_dosen->detail->find($id);

        $data_validation = [
            'kelompok_dosen_id' => $idKelompokDosen,
            'kelompok_dosen_detail_id' => $id
        ];
        $rules = [
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'kelompok_dosen_detail_id' => ['required', 'exists:kelompok_dosen_detail,id,deleted_at,NULL']
        ];
        $validator = Validator::make($data_validation, $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect('/penjadwalan/kelompok-dosen')->with($message);
        }

        $mata_kuliah = $this->mata_kuliah_model->all();
        $dosen = $this->dosen_model->all();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Ubah Kelompok Dosen Detail',
            'breadcrumbs' => $breadcrumbs,
            'kelompok_dosen' => $kelompok_dosen,
            'kelompok_dosen_detail' => $kelompok_dosen_detail,
            'mata_kuliah' => $mata_kuliah,
            'dosen' => $dosen,
            'action' => URL::to('/penjadwalan/kelompok-dosen/detail/' . $idKelompokDosen . '/ubah/' . $id)
        ];
        return view('kelompok_dosen_detail.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idKelompokDosen, $id)
    {
        $rules = [
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kelompok' => ['required'],
            'kapasitas' => ['required', 'numeric', 'min:1'],
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'kelompok_dosen_detail_id' => ['required', 'exists:kelompok_dosen_detail,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all() + [
            'kelompok_dosen_id' => $idKelompokDosen,
            'kelompok_dosen_detail_id' => $id
        ], $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect()->back()->with($message)->withErrors($validator->errors())->withInput();
        }

        try {
            $kelompok_dosen = $this->kelompok_dosen_model->find($idKelompokDosen);
            $kelompok_dosen_detail = $kelompok_dosen->detail->find($id);
            $kelompok_dosen_detail->kode_matkul = $request->kode_matkul;
            $kelompok_dosen_detail->kode_dosen = $request->kode_dosen;
            $kelompok_dosen_detail->kelompok = $request->kelompok;
            $kelompok_dosen_detail->kapasitas = $request->kapasitas;
            $kelompok_dosen_detail->save();
            $message = [
                'success' => "Data berhasil diubah"
            ];
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            Log::error($e->getMessage());
        }

        return redirect('/penjadwalan/kelompok-dosen/detail/' . $kelompok_dosen->id)->with($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $idDetail)
    {
        $kelompok_dosen = $this->kelompok_dosen_model->find($id);
        if (!$kelompok_dosen) {
            $message = [
                'error' => "<strong>Dosen Tidak Ditemukan!</strong>"
            ];
            return redirect('/penjadwalan/kelompok-dosen/')->with($message);
        }

        $kelompok_dosen_detail = $kelompok_dosen->detail->find($idDetail);
        if (!$kelompok_dosen_detail) {
            $message = [
                'error' => "<strong>Dosen Tidak Ditemukan!</strong>"
            ];
            return redirect('/penjadwalan/kelompok-dosen/detail/' . $id)->with($message);
        }

        $status = $kelompok_dosen_detail->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Dosen!'</strong>" . 'Dosen ' . $kelompok_dosen_detail->getOriginal('nama_dosen') . ' telah dihapus.'
            ];
            return redirect('/penjadwalan/kelompok-dosen/detail/' . $id)->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Dosen!'
            ];
            return redirect('/penjadwalan/kelompok-dosen/detail/' . $id)->with($message);
        }
    }
}
