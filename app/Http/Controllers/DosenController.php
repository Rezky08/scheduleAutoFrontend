<?php

namespace App\Http\Controllers;

use App\Dosen;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    private $breadcrumbs_helper;
    private $dosen_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->dosen_model = new Dosen();
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
            'title' => 'Dosen',
            'breadcrumbs' => $breadcrumbs,
            'dosen' => $this->dosen_model->paginate(15)
        ];

        echo "<script>var dosen = " . $data['dosen']->toJson() . "</script>";

        return view('dosen.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Dosen",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/dosen/tambah')
        ];
        return view('dosen.form', $data);
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
            'kode_dosen' => ['required', 'unique:dosen,kode_dosen,NULL,id,deleted_at,NULL', 'numeric'],
            'nama_dosen' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except("_token");
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->dosen_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Dosen!'</strong>" . 'Dosen ' . $dataInsert['nama_dosen'] . ' telah ditambahkan.'
                ];
                return redirect('/master/dosen')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Dosen!'
                ];
                return redirect('/master/dosen')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/dosen')->with($message);
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
        $dosen = $this->dosen_model->find($id);
        $data = [
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'dosen' => $dosen,
            'title' => 'Detail ' . $dosen->nama_dosen
        ];
        return view('dosen.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $dosen = $this->dosen_model->find($id);

        if (!$dosen) {
            $message = [
                'error' => "<strong>Dosen Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/dosen')->with($message);
        }
        $data = [
            'title' => "Ubah Dosen",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'dosen' => $dosen,
            'action' => URL::to('/master/dosen/ubah/' . $id)
        ];
        return view('dosen.form', $data);
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
        $dosen = $this->dosen_model->find($id);
        $rules = [
            'kode_dosen' => ['required', 'unique:dosen,kode_dosen,' . $id . ',id,deleted_at,NULL', 'numeric'],
            'nama_dosen' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dosen->kode_dosen = $request->kode_dosen;
            $dosen->nama_dosen = $request->nama_dosen;

            $status = $dosen->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Dosen!'</strong>" . 'Dosen ' . $dosen->getOriginal('nama_dosen') . ' telah diubah.'
                ];
                return redirect('/master/dosen')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Dosen!'
                ];
                return redirect('/master/dosen')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/dosen')->with($message);
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
        $dosen = $this->dosen_model->find($id);

        if (!$dosen) {
            $message = [
                'error' => "<strong>Dosen Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/dosen')->with($message);
        }

        $status = $dosen->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Dosen!'</strong>" . 'Dosen ' . $dosen->getOriginal('nama_dosen') . ' telah dihapus.'
            ];
            return redirect('/master/dosen')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Dosen!'
            ];
            return redirect('/master/dosen')->with($message);
        }
    }
}
