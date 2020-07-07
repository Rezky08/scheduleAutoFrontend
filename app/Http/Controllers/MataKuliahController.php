<?php

namespace App\Http\Controllers;

use App\Matakuliah;
use App\ProgramStudi;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    private $breadcrumbs_helper;
    private $mata_kuliah_model;
    private $program_studi_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->mata_kuliah_model = new Matakuliah();
        $this->program_studi_model = new ProgramStudi();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Mata Kuliah',
            'breadcrumbs' => $breadcrumbs,
            'mata_kuliah' => $this->mata_kuliah_model->paginate($request->perpage ? $request->perpage : 15)
        ];

        echo "<script>var mata_kuliah = " . $data['mata_kuliah']->toJson() . "</script>";

        return view('mata_kuliah.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Mata Kuliah",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/mata-kuliah/tambah'),
            'program_studi' => $this->program_studi_model->all()
        ];
        return view('mata_kuliah.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'kode_matkul' => ['required', 'unique:mata_kuliah,kode_matkul,NULL,id,deleted_at,NULL', 'max:10'],
            'sks_matkul' => ['required', 'numeric'],
            'nama_matkul' => ['required'],
            'status_matkul' => ['boolean'],
            'lab_matkul' => ['boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi,deleted_at,NULL'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except(["_token"]);
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->mata_kuliah_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Mata Kuliah!'</strong>" . 'Mata Kuliah ' . $dataInsert['nama_matkul'] . '(' . $dataInsert['kode_matkul'] . ') telah ditambahkan.'
                ];
                return redirect('/master/mata-kuliah')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Mata Kuliah!'
                ];
                return redirect('/master/mata-kuliah')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/mata-kuliah')->with($message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $mata_kuliah = $this->mata_kuliah_model->find($id);
        $data = [
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'mata_kuliah' => $mata_kuliah,
            'title' => 'Detail ' . $mata_kuliah->nama_mata_kuliah
        ];
        return view('mata_kuliah.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $mata_kuliah = $this->mata_kuliah_model->find($id);

        if (!$mata_kuliah) {
            $message = [
                'error' => "<strong>Mata Kuliah Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/mata-kuliah')->with($message);
        }
        $data = [
            'title' => "Ubah Mata Kuliah",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'mata_kuliah' => $mata_kuliah,
            'program_studi' => $this->program_studi_model->all(),
            'action' => URL::to('/master/mata-kuliah/ubah/' . $id)
        ];
        return view('mata_kuliah.form', $data);
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
        $mata_kuliah = $this->mata_kuliah_model->find($id);
        $rules = [
            'kode_matkul' => ['required', 'unique:mata_kuliah,kode_matkul,' . $id . ',id,deleted_at,NULL', 'max:10'],
            'sks_matkul' => ['required', 'numeric', 'min:1'],
            'nama_matkul' => ['required'],
            'status_matkul' => ['required', 'boolean'],
            'lab_matkul' => ['required', 'boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi,deleted_at,NULL'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $mata_kuliah->kode_matkul = $request->kode_matkul;
            $mata_kuliah->sks_matkul = $request->sks_matkul;
            $mata_kuliah->nama_matkul = $request->nama_matkul;
            $mata_kuliah->status_matkul = $request->status_matkul;
            $mata_kuliah->lab_matkul = $request->lab_matkul;
            $mata_kuliah->kode_prodi = $request->kode_prodi;


            $status = $mata_kuliah->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Mata Kuliah!'</strong>" . 'Mata Kuliah ' . $mata_kuliah->getOriginal('nama_matkul') . '(' . $mata_kuliah->getOriginal('kode_matkul') . ') telah diubah.'
                ];
                return redirect('/master/mata-kuliah')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Mata Kuliah!'
                ];
                return redirect('/master/mata-kuliah')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/mata-kuliah')->with($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mata_kuliah = $this->mata_kuliah_model->find($id);

        if (!$mata_kuliah) {
            $message = [
                'error' => "<strong>Mata Kuliah Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/mata-kuliah')->with($message);
        }

        $status = $mata_kuliah->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Mata Kuliah!'</strong>" . 'Mata Kuliah ' . $mata_kuliah->getOriginal('nama_matkul') . '(' . $mata_kuliah->getOriginal('kode_matkul') . ') telah dihapus.'
            ];
            return redirect('/master/mata-kuliah')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Mata Kuliah!'
            ];
            return redirect('/master/mata-kuliah')->with($message);
        }
    }
}
