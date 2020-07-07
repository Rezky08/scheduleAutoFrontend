<?php

namespace App\Http\Controllers;

use App\Dosen;
use App\Matakuliah;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DosenMataKuliahController extends Controller
{
    private $breadcrumbs_helper;
    private $dosen_model;
    private $mata_kuliah_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->dosen_model = new Dosen();
        $this->mata_kuliah_model = new Matakuliah();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDosenMataKuliah(Request $request, $id)
    {
        $dosen = $this->dosen_model->find($id);
        $mata_kuliah = $this->mata_kuliah_model->all();
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $num_form = 1 + $dosen->dosen_matkul->count();
        if ($request->num_form && $request->last_num_form) {
            $num_form = $request->num_form + $request->last_num_form;
        }
        $data = [
            'title' => 'dosen mata kuliah',
            'dosen' => $dosen,
            'dosen_matkul' => $dosen->dosen_matkul,
            'breadcrumbs' => $breadcrumbs,
            'num_form' => $num_form,
            'mata_kuliah' => $mata_kuliah,
            'action' => URL::to('/master/dosen/detail/' . $id . '/mata-kuliah')
        ];
        return view('dosen_mata_kuliah.dosen_form_create', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMataKuliahDosen(Request $request, $id)
    {
        $mata_kuliah = $this->mata_kuliah_model->find($id);
        $dosen = $this->dosen_model->all();
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $num_form = 1 + $mata_kuliah->dosen_matkul->count();
        if ($request->num_form && $request->last_num_form) {
            $num_form = $request->num_form + $request->last_num_form;
        }
        $data = [
            'title' => 'dosen mata kuliah',
            'dosen' => $dosen,
            'dosen_matkul' => $mata_kuliah->dosen_matkul,
            'breadcrumbs' => $breadcrumbs,
            'num_form' => $num_form,
            'mata_kuliah' => $mata_kuliah,
            'action' => URL::to('/master/mata-kuliah/detail/' . $id . '/dosen')
        ];
        return view('dosen_mata_kuliah.mata_kuliah_form_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDosenMataKuliah(Request $request, $id)
    {
        $dosen = $this->dosen_model->find($id);
        $rules = [
            'mata_kuliah.*.kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', 'distinct']
        ];
        $message = [
            'mata_kuliah.*.kode_matkul.unique' => 'Mata Kuliah has already been taken.'
        ];
        foreach ($request->mata_kuliah as $index => $item) {
            $rules['mata_kuliah.' . $index . '.kode_matkul'] = [
                Rule::unique('dosen_mata_kuliah')->where(function ($query) use ($dosen, $item) {
                    $query = $query->where('kode_matkul', $item['kode_matkul'])->where('kode_dosen', $dosen->kode_dosen)->where('deleted_at', NULL);
                    if (isset($item['id'])) {
                        $query = $query->where('id', '!=', $item['id']);
                    }
                    return $query;
                })
            ];
        }
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $mata_kuliah = collect($request->mata_kuliah);
        $mata_kuliah_update = $mata_kuliah->filter(function ($item) {
            if (isset($item['id'])) {
                return $item;
            }
        });

        $mata_kuliah_update->map(function ($item, $index) use ($dosen) {
            try {
                $dosen_matkul = $dosen->dosen_matkul->find($item['id']);
                $dosen_matkul->kode_matkul = $item['kode_matkul'];
                $status = $dosen_matkul->save();
            } catch (Exception $e) {
                $message = [
                    'error' => $e->getMessage()
                ];
                return redirect()->back()->with($message)->withInput();
            }
        });

        $mata_kuliah_insert = $mata_kuliah->filter(function ($item) {
            if (!isset($item['id'])) {
                return $item;
            }
        });

        $data_insert = $mata_kuliah_insert->toArray();
        foreach ($data_insert as $index => $item) {
            $data_insert[$index] = [
                'kode_matkul' => $item['kode_matkul'],
                'kode_dosen' => $dosen->kode_dosen,
                'created_at' => new \DateTime
            ];
        }
        try {
            $status = $this->dosen_model->dosen_matkul()->insert($data_insert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Mata Kuliah Diampu!'</strong>"
                ];
                return redirect('/master/dosen/detail/' . $id)->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect()->back()->with($message);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMataKuliahDosen(Request $request, $id)
    {
        $mata_kuliah = $this->mata_kuliah_model->find($id);
        $rules = [
            'dosen.*.kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL', 'distinct']
        ];
        $message = [
            'dosen.*.kode_dosen.unique' => 'Dosen has already been taken.'
        ];
        foreach ($request->dosen as $index => $item) {
            $rules['dosen.' . $index . '.kode_dosen'] = [
                Rule::unique('dosen_mata_kuliah')->where(function ($query) use ($mata_kuliah, $item) {
                    $query = $query->where('kode_dosen', $item['kode_dosen'])->where('kode_matkul', $mata_kuliah->kode_matkul)->where('deleted_at', NULL);
                    if (isset($item['id'])) {
                        $query = $query->where('id', '!=', $item['id']);
                    }
                    return $query;
                })
            ];
        }
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $dosen = collect($request->dosen);
        $dosen_update = $dosen->filter(function ($item) {
            if (isset($item['id'])) {
                return $item;
            }
        });

        $dosen_update->map(function ($item, $index) use ($mata_kuliah) {
            try {
                $dosen_matkul = $mata_kuliah->dosen_matkul->find($item['id']);
                $dosen_matkul->kode_dosen = $item['kode_dosen'];
                $status = $dosen_matkul->save();
            } catch (Exception $e) {
                $message = [
                    'error' => $e->getMessage()
                ];
                return redirect()->back()->with($message)->withInput();
            }
        });

        $dosen_insert = $dosen->filter(function ($item) {
            if (!isset($item['id'])) {
                return $item;
            }
        });

        $data_insert = $dosen_insert->toArray();
        foreach ($data_insert as $index => $item) {
            $data_insert[$index] = [
                'kode_matkul' => $mata_kuliah->kode_matkul,
                'kode_dosen' => $item['kode_dosen'],
                'created_at' => new \DateTime
            ];
        }
        try {
            $status = $this->mata_kuliah_model->dosen_matkul()->insert($data_insert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Pengampu Mata Kuliah!'</strong>"
                ];
                return redirect('/master/mata-kuliah/detail/' . $id)->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect()->back()->with($message);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyDosenMataKuliah($iddosen, $id)
    {
        $dosen = $this->dosen_model->find($iddosen);
        $dosen_matkul = $dosen->dosen_matkul()->find($id);

        if (!$dosen_matkul) {
            $message = [
                'error' => "<strong>Mata Kuliah Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/dosen')->with($message);
        }

        $status = $dosen_matkul->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Mata Kuliah!'</strong>" . 'Mata Kuliah ' . $dosen_matkul->matkul->nama_matkul . ' telah dihapus.'
            ];
            return redirect('/master/dosen/detail/' . $iddosen)->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Mata Kuliah!'
            ];
            return redirect('/master/dosen/detail/' . $iddosen)->with($message);
        }
    }
}
