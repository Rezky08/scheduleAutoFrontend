<?php

namespace App\Http\Controllers;

use App\ProgramStudi;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ProgramStudiController extends Controller
{
    private $breadcrumbs_helper;
    private $program_studi_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
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
            'title' => 'Program Studi',
            'breadcrumbs' => $breadcrumbs,
            'program_studi' => $this->program_studi_model->paginate(15)
        ];

        echo "<script>var program_studi = " . $data['program_studi']->toJson() . "</script>";

        return view('program_studi.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Program Studi",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/program-studi/tambah')
        ];
        return view('program_studi.form', $data);
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
            'kode_prodi' => ['required', 'filled', 'unique:program_studi,kode_prodi,NULL,kode_prodi,deleted_at,NULL'],
            'nama_prodi' => ['required', 'filled', 'max:100'],
            'keterangan' => []
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except(["_token", "keterangan"]);
            $dataInsert['keterangan_prodi'] = $request->keterangan;
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->program_studi_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Program Studi!'</strong>" . 'Program Studi ' . $dataInsert['nama_prodi'] . '(' . $dataInsert['kode_prodi'] . ') telah ditambahkan.'
                ];
                return redirect('/master/program-studi')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Program Studi!'
                ];
                return redirect('/master/program-studi')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/program-studi')->with($message);
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
    public function edit(Request $request, $id)
    {
        $program_studi = $this->program_studi_model->find($id);

        if (!$program_studi) {
            $message = [
                'error' => "<strong>Program Studi Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/program-studi')->with($message);
        }
        $data = [
            'title' => "Ubah Program Studi",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'program_studi' => $program_studi,
            'action' => URL::to('/master/program-studi/ubah/' . $id)
        ];
        return view('program_studi.form', $data);
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
        $program_studi = $this->program_studi_model->find($id);
        $rules = [
            'kode_prodi' => ['required', 'filled', 'unique:program_studi,kode_prodi,' . $id . ',id,deleted_at,NULL'],
            'nama_prodi' => ['required', 'filled', 'max:100'],
            'keterangan' => []
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $program_studi->kode_prodi = $request->kode_prodi;
            $program_studi->nama_prodi = $request->nama_prodi;
            $program_studi->keterangan_prodi = $request->keterangan;

            $status = $program_studi->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Program Studi!'</strong>" . 'Program Studi ' . $program_studi->getOriginal('nama_prodi') . '(' . $program_studi->getOriginal('kode_prodi') . ') telah diubah.'
                ];
                return redirect('/master/program-studi')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Program Studi!'
                ];
                return redirect('/master/program-studi')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/program-studi')->with($message);
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
        $program_studi = $this->program_studi_model->find($id);

        if (!$program_studi) {
            $message = [
                'error' => "<strong>Program Studi Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/program-studi')->with($message);
        }

        $status = $program_studi->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Program Studi!'</strong>" . 'Program Studi ' . $program_studi->getOriginal('nama_prodi') . '(' . $program_studi->getOriginal('kode_prodi') . ') telah dihapus.'
            ];
            return redirect('/master/program-studi')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Program Studi!'
            ];
            return redirect('/master/program-studi')->with($message);
        }
    }
}
