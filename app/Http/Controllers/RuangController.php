<?php

namespace App\Http\Controllers;

use App\Ruang;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class RuangController extends Controller
{
    private $breadcrumbs_helper;
    private $ruang_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->ruang_model = new Ruang();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->searchbox) {
            $ruang = $this->search($request->searchbox);
        } else {
            $ruang = $this->ruang_model;
        }
        $ruang = $ruang->paginate(15);
        $ruang->appends($request->all())->render();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Ruang',
            'breadcrumbs' => $breadcrumbs,
            'ruang' => $ruang
        ];

        echo "<script>var ruang = " . $data['ruang']->toJson() . "</script>";

        return view('ruang.list', $data);
    }

    public function search($string = "")
    {
        $ruang = $this->ruang_model->where('nama_ruang', 'like', '%' . $string . '%')->orwhere('kapasitas', 'like', '%' . $string . '%');
        return $ruang;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Ruang",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/ruang/tambah')
        ];
        return view('ruang.form', $data);
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
            'nama_ruang' => ['required', 'unique:ruang,nama_ruang,NULL,id,deleted_at,NULL'],
            'kapasitas' => ['required', 'min:1', 'integer'],
            'keterangan' => [],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except("_token");
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->ruang_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Ruang!'</strong>" . 'Ruang ' . $dataInsert['nama_ruang'] . ' telah ditambahkan.'
                ];
                return redirect('/master/ruang')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Ruang!'
                ];
                return redirect('/master/ruang')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/ruang')->with($message);
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
        $ruang = $this->ruang_model->find($id);

        if (!$ruang) {
            $message = [
                'error' => "<strong>Ruang Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/ruang')->with($message);
        }
        $data = [
            'title' => "Ubah Ruang",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'ruang' => $ruang,
            'action' => URL::to('/master/ruang/ubah/' . $id)
        ];
        return view('ruang.form', $data);
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
        $ruang = $this->ruang_model->find($id);
        $rules = [
            'nama_ruang' => ['required', 'unique:ruang,nama_ruang,' . $id . ',id,deleted_at,NULL'],
            'kapasitas' => ['required', 'min:1', 'integer'],
            'keterangan' => [],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $ruang->nama_ruang = $request->nama_ruang;
            $ruang->kapasitas = $request->kapasitas;
            $ruang->keterangan = $request->keterangan;

            $status = $ruang->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Ruang!'</strong>" . 'Ruang ' . $ruang->getOriginal('nama_ruang') . ' telah diubah.'
                ];
                return redirect('/master/ruang')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Ruang!'
                ];
                return redirect('/master/ruang')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/ruang')->with($message);
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
        $ruang = $this->ruang_model->find($id);

        if (!$ruang) {
            $message = [
                'error' => "<strong>Ruang Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/ruang')->with($message);
        }

        $status = $ruang->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Ruang!'</strong>" . 'Ruang ' . $ruang->getOriginal('nama_ruang') . ' telah dihapus.'
            ];
            return redirect('/master/ruang')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Ruang!'
            ];
            return redirect('/master/ruang')->with($message);
        }
    }
}
