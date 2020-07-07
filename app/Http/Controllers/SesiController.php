<?php

namespace App\Http\Controllers;

use App\Sesi;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class SesiController extends Controller
{
    private $breadcrumbs_helper;
    private $sesi_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->sesi_model = new Sesi();
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
            'title' => 'Sesi',
            'breadcrumbs' => $breadcrumbs,
            'sesi' => $this->sesi_model->paginate(15)
        ];

        echo "<script>var sesi = " . $data['sesi']->toJson() . "</script>";

        return view('sesi.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Sesi",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/sesi/tambah')
        ];
        return view('sesi.form', $data);
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
            'sesi_mulai' => ['required', 'date_format:H:i', 'unique:sesi,sesi_mulai,NULL,id,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'date_format:H:i', 'unique:sesi,sesi_selesai,NULL,id,deleted_at,NULL'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except("_token");
            $dataInsert['created_at'] = new \DateTime;
            $status = $this->sesi_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Sesi!'</strong>" . 'Sesi ' . $dataInsert['sesi_mulai'] . ' - ' . $dataInsert['sesi_selesai'] . ' telah ditambahkan.'
                ];
                return redirect('/master/sesi')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Sesi!'
                ];
                return redirect('/master/sesi')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/sesi')->with($message);
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
        $sesi = $this->sesi_model->find($id);

        if (!$sesi) {
            $message = [
                'error' => "<strong>Sesi Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/sesi')->with($message);
        }
        $data = [
            'title' => "Ubah Sesi",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'sesi' => $sesi,
            'action' => URL::to('/master/sesi/ubah/' . $id)
        ];
        return view('sesi.form', $data);
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
        $sesi = $this->sesi_model->find($id);
        if ($request->sesi_mulai && $request->sesi_selesai) {
            $reqData = $request->all();
            $reqData['sesi_mulai'] = date('H:i', strtotime($reqData['sesi_mulai']));
            $reqData['sesi_selesai'] = date('H:i', strtotime($request['sesi_selesai']));
            $request->merge($reqData);
        }
        $rules = [
            'sesi_mulai' => ['required', 'date_format:H:i', 'unique:sesi,sesi_mulai,' . $id . ',id,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'date_format:H:i', 'unique:sesi,sesi_selesai,' . $id . ',id,deleted_at,NULL'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $sesi->sesi_mulai = $request->sesi_mulai;
            $sesi->sesi_selesai = $request->sesi_selesai;

            $status = $sesi->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Sesi!'</strong>" . 'Sesi ' . $sesi->getOriginal('sesi_mulai') . '-' . $sesi->getOriginal('sesi_selesai') . ' telah diubah.'
                ];
                return redirect('/master/sesi')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Sesi!'
                ];
                return redirect('/master/sesi')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/sesi')->with($message);
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
        $sesi = $this->sesi_model->find($id);

        if (!$sesi) {
            $message = [
                'error' => "<strong>Sesi Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/sesi')->with($message);
        }

        $status = $sesi->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Sesi!'</strong>" . 'Sesi ' . $sesi->getOriginal('sesi_mulai') . '-' . $sesi->getOriginal('sesi_selesai') . ' telah dihapus.'
            ];
            return redirect('/master/sesi')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Sesi!'
            ];
            return redirect('/master/sesi')->with($message);
        }
    }
}
