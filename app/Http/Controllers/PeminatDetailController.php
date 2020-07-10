<?php

namespace App\Http\Controllers;

use App\Matakuliah;
use App\Peminat;
use App\PeminatDetail;
use App\SemesterDetail;
use BreadCrumbs;
use Doctrine\Inflector\Rules\English\Rules;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeminatDetailController extends Controller
{
    private $breadcrumbs_helper;
    private $peminat_model;
    private $peminat_detail_model;
    private $semester_model;
    private $mata_kuliah_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->peminat_detail_model = new PeminatDetail();
        $this->peminat_model = new Peminat();
        $this->semester_model = new SemesterDetail();
        $this->mata_kuliah_model = new Matakuliah();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $peminat = $this->peminat_model->find($id);
        $peminat_detail = $this->peminat_detail_model->where('peminat_id', $peminat->id);
        if ($request->searchbox) {
            $peminat_detail = $this->search($peminat_detail, $request->searchbox);
        }
        $peminat_detail = $peminat_detail->paginate(15);
        $peminat_detail->appends($request->all())->render();

        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);

        $data = [
            'title' => 'Peminat Detail',
            'breadcrumbs' => $breadcrumbs,
            'peminat' => $peminat,
            'peminat_detail' => $peminat_detail,
            'action' => URL::to('/master/peminat/detail/' . $id . '/tambah/batch/preview')
        ];
        echo "<script>var peminat_detail = " . $peminat->peminat_detail->toJson() . "</script>";
        return view('peminat_detail.list', $data);
    }


    public function search($model, $string = "")
    {
        $model->leftJoin('mata_kuliah', 'mata_kuliah.kode_matkul', '=', 'peminat_detail.kode_matkul');
        $peminat_detail = $model->where('peminat_detail.kode_matkul', 'like', '%' . $string . '%')->orwhere('mata_kuliah.nama_matkul', 'like', '%' . $string . '%');
        return $peminat_detail;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idPeminat)
    {
        $num_form = 1;
        if ($request->num_form && $request->last_num_form) {
            $num_form = $request->num_form + $request->last_num_form;
        }
        $peminat = $this->peminat_model->find($idPeminat);
        $semester = $this->semester_model->all();
        $mata_kuliah = $this->mata_kuliah_model->all();
        $data = [
            'title' => "Peminat Detail",
            'breadcrumbs' => $this->breadcrumbs_helper->make($request->segments()),
            'action' => URL::to('/master/peminat/detail/' . $idPeminat . '/tambah'),
            'semester' => $semester,
            'peminat' => $peminat,
            'mata_kuliah' => $mata_kuliah,
            'num_form' => $num_form
        ];
        return view('peminat_detail.form_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idPeminat)
    {
        $rules = [
            'peminat_detail.*.kode_matkul' => ['required', 'distinct', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'peminat_detail.*.jumlah_peminat' => ['required', 'integer'],
        ];
        foreach ($request->peminat_detail as $index => $item) {
            $rules['peminat_detail.' . $index . '.kode_matkul'] = [
                Rule::unique('peminat_detail')->where(function ($query) use ($item, $idPeminat) {
                    return $query->where('kode_matkul', $item['kode_matkul'])->where('peminat_id', $idPeminat)->where('deleted_at', NULL);
                })
            ];
        }
        $message = [
            'peminat_detail.*.jumlah_peminat.required' => "jumlah peminat field is required.",
            'peminat_detail.*.kode_matkul.required' => "Mata Kuliah field is required.",
            'peminat_detail.*.kode_matkul.distinct' => "Mata Kuliah field has a duplicate value.",
            'peminat_detail.*.kode_matkul.unique' => "Mata Kuliah field has already been taken.",
            'peminat_detail.*.kode_matkul.exists' => "Mata Kuliah is Invalid."
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $dataInsert = $request->peminat_detail;
            foreach ($dataInsert as $index => $item) {
                $dataInsert[$index]['created_at'] = new \DateTime;
                $dataInsert[$index]['peminat_id'] = $idPeminat;
            }
            $status = $this->peminat_detail_model->insert($dataInsert);
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Tambah Peminat Mata Kuliah!'</strong>"
                ];
                return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Tambah Peminat Mata Kuliah!'
                ];
                return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
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
    public function edit(Request $request, $idPeminat, $id)
    {
        $peminat = $this->peminat_model->find($idPeminat);
        $semester = $this->semester_model->all();
        $mata_kuliah = $this->mata_kuliah_model->all();
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
            'peminat_detail' => $peminat->peminat_detail->find($id),
            'action' => URL::to('/master/peminat/detail/' . $idPeminat . '/ubah/' . $id),
            'semester' => $semester,
            'mata_kuliah' => $mata_kuliah,
        ];
        return view('peminat_detail.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idPeminat, $id)
    {
        $peminat = $this->peminat_model->find($idPeminat);
        $peminat_detail = $peminat->peminat_detail->find($id);
        $rules = [
            'kode_matkul' => ['required', Rule::unique('peminat_detail')->where(function ($query) use ($request, $idPeminat, $id) {
                return $query->where('kode_matkul', $request->kode_matkul)->where('peminat_id', $idPeminat)->where('id', '!=', $id)->where('deleted_at', NULL);
            })],
            'jumlah_peminat' => ['required', 'numeric'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        try {
            $mata_kuliah = $peminat_detail->mata_kuliah;
            $peminat_detail->kode_matkul = $request->kode_matkul;
            $peminat_detail->jumlah_peminat = $request->jumlah_peminat;

            $status = $peminat_detail->save();
            if ($status) {
                $message = [
                    'success' => "<strong>'Berhasil Ubah Peminat! '</strong>" . 'Peminat Mata Kuliah ' . $mata_kuliah->nama_matkul . ' Semester (' . $mata_kuliah->kode_matkul . ') Semester ' . $peminat->semester_detail->keterangan . ' telah diubah.'
                ];
                return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
            } else {
                $message = [
                    'error' => 'Gagal Ubah Peminat!'
                ];
                return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idPeminat, $id)
    {
        $peminat = $this->peminat_model->find($idPeminat);
        $peminat_detail = $peminat->peminat_detail->find($id);

        if (!$peminat_detail) {
            $message = [
                'error' => "<strong>Peminat Mata Kuliah Tidak Ditemukan!</strong>"
            ];
            return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
        }

        $status = $peminat_detail->delete();
        if ($status) {
            $mata_kuliah = $peminat_detail->mata_kuliah;
            $message = [
                'success' => "<strong>'Berhasil Hapus Peminat Mata Kuliah!'</strong>" . 'Peminat Mata Kuliah ' . $mata_kuliah->nama_matkul . ' (' . $mata_kuliah->kode_matkul . ') telah dihapus.'
            ];
            return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Peminat!'
            ];
            return redirect('/master/peminat/detail/' . $idPeminat)->with($message);
        }
    }
}
