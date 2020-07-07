<?php

namespace App\Http\Controllers;

use App\Hari;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class HariController extends Controller
{
    private $breadcrumbs_helper;
    private $hari_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->hari_model = new Hari();
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
            'title' => 'Hari',
            'breadcrumbs' => $breadcrumbs,
            'hari' => $this->hari_model->paginate(15)
        ];

        echo "<script>var hari = " . $data['hari']->toJson() . "</script>";

        return view('hari.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Hari",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/hari/tambah')
        ];
        return view('hari.form', $data);
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
            'nama_hari' => ['required', 'unique:hari,nama_hari,NULL,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except("_token");
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->hari_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Hari!'</strong>" . 'Hari ' . $dataInsert['nama_hari'] . ' telah ditambahkan.'
                ];
                return redirect('/master/hari')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Hari!'
                ];
                return redirect('/master/hari')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/hari')->with($message);
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
        $hari = $this->hari_model->find($id);

        if (!$hari) {
            $message = [
                'error' => "<strong>Hari Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/hari')->with($message);
        }
        $data = [
            'title' => "Ubah Hari",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'hari' => $hari,
            'action' => URL::to('/master/hari/ubah/' . $id)
        ];
        return view('hari.form', $data);
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
        $hari = $this->hari_model->find($id);
        $rules = [
            'nama_hari' => ['required', 'unique:hari,nama_hari,' . $id . ',id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $hari->nama_hari = $request->nama_hari;

            $status = $hari->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Hari!'</strong>" . 'Hari ' . $hari->getOriginal('nama_hari') . ' telah diubah.'
                ];
                return redirect('/master/hari')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Hari!'
                ];
                return redirect('/master/hari')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/hari')->with($message);
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
        $hari = $this->hari_model->find($id);

        if (!$hari) {
            $message = [
                'error' => "<strong>Hari Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/hari')->with($message);
        }

        $status = $hari->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Hari!'</strong>" . 'Hari ' . $hari->getOriginal('nama_hari') . ' telah dihapus.'
            ];
            return redirect('/master/hari')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Hari!'
            ];
            return redirect('/master/hari')->with($message);
        }
    }
}
