<?php

namespace App\Http\Controllers;

use App\Peminat;
use App\SemesterDetail;
use BreadCrumbs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeminatController extends Controller
{
    private $breadcrumbs_helper;
    private $peminat_model;
    private $semester_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->peminat_model = new Peminat();
        $this->semester_model = new SemesterDetail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->searchbox) {
            $peminat = $this->search($request->searchbox);
        } else {
            $peminat = $this->peminat_model;
        }
        $peminat = $peminat->paginate(15);
        $peminat->appends($request->all())->render();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Peminat',
            'breadcrumbs' => $breadcrumbs,
            'peminat' => $peminat
        ];

        echo "<script>var peminat = " . $data['peminat']->toJson() . "</script>";

        return view('peminat.list', $data);
    }


    public function search($string = "")
    {
        $peminat = $this->peminat_model->where('tahun_ajaran', 'like', '%' . $string . '%')->orwhere('semester', 'like', '%' . $string . '%');
        return $peminat;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => "Ubah Peminat",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/peminat/tambah'),
            'semester' => $this->semester_model->all()
        ];
        return view('peminat.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messageValidation = [
            'tahun_ajaran.unique' => 'Tahun ajaran dan semester sudah digunakan.'
        ];
        $rules = [
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', Rule::unique('peminat')->where(function ($query) use ($request) {
                return $query->where('tahun_ajaran', $request->tahun_ajaran)->where('semester', $request->semester)->where('deleted_at', NULL);
            })],
            'semester' => ['required', 'in:E,O'],
        ];
        $validator = Validator::make($request->all(), $rules, $messageValidation);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->except("_token");
            $dataInsert['created_at'] = new \DateTime;
            $idPeminat = $this->peminat_model->insertGetId($dataInsert);
            if ($idPeminat) {
                $peminat = $this->peminat_model->find($idPeminat);
                $message = [
                    'success' => "<strong>'Berhasil Tambah Peminat!'</strong>" . 'Peminat tahun ajaran ' . $peminat->tahun_ajaran . ' semester ' . $peminat->semester_detail->keterangan . ' telah ditambahkan.'
                ];
                return redirect('/master/peminat')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Peminat!'
                ];
                return redirect('/master/peminat')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/peminat')->with($message);
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
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $peminat = $this->peminat_model->find($id);
        $data = [
            'title' => 'Peminat Detail',
            'breadcrumbs' => $breadcrumbs,
            'peminat' => $peminat,
            'peminat_detail' => $peminat->peminat_detail()->paginate(15)
        ];
        return view('peminat_detail.list', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $peminat = $this->peminat_model->find($id);

        if (!$peminat) {
            $message = [
                'error' => "<strong>Peminat Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/peminat')->with($message);
        }
        $data = [
            'title' => "Ubah Peminat",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'peminat' => $peminat,
            'action' => URL::to('/master/peminat/ubah/' . $id),
            'semester' => $this->semester_model->all()
        ];
        return view('peminat.form', $data);
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
        $peminat = $this->peminat_model->find($id);
        $messageValidation = [
            'tahun_ajaran.unique' => 'Tahun ajaran dan semester sudah digunakan.'
        ];
        $rules = [
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', Rule::unique('peminat')->where(function ($query) use ($request, $id) {
                return $query->where('tahun_ajaran', $request->tahun_ajaran)->where('semester', $request->semester)->where('id', '!=', $id)->where('deleted_at', NULL);
            })],
            'semester' => ['required', 'in:E,O'],
        ];
        $validator = Validator::make($request->all(), $rules, $messageValidation);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $peminat->tahun_ajaran = $request->tahun_ajaran;
            $peminat->semester = $request->semester;

            $status = $peminat->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Peminat!'</strong>" . 'Peminat ' . $peminat->getOriginal('tahun_ajaran') . ' Semester ' . $peminat->semester_detail->keterangan . ' telah diubah.'
                ];
                return redirect('/master/peminat')->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Peminat!'
                ];
                return redirect('/master/peminat')->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/peminat')->with($message);
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
        $peminat = $this->peminat_model->find($id);

        if (!$peminat) {
            $message = [
                'error' => "<strong>Peminat Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/peminat')->with($message);
        }

        $status = $peminat->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Peminat!'</strong>" . 'Peminat tahun ajaran ' . $peminat->tahun_ajaran . ' semester ' . $peminat->semester_detail->keterangan . ' telah dihapus.'
            ];
            return redirect('/master/peminat')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Peminat!'
            ];
            return redirect('/master/peminat')->with($message);
        }
    }
}
