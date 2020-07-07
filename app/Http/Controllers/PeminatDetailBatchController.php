<?php

namespace App\Http\Controllers;

use App\Imports\PeminatDetailImport;
use App\Peminat;
use App\PeminatDetail;
use BreadCrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PeminatDetailBatchController extends Controller
{
    private $peminat_model;
    private $reader;
    private $breadcrumbs_helper;
    function __construct()
    {
        $this->peminat_model = new Peminat();
        $this->breadcrumbs_helper = new BreadCrumbs();
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

    public function validationInput(Request $request, $id)
    {

        $rules = [
            'file_peminat_detail' => ['required', 'file', 'filled', 'mimes:xls,xlsx'],
            'data_row' => ['required', 'filled', 'numeric'],
            'kode_matkul_column' => ['required', 'filled', 'alpha'],
            'jumlah_peminat_column' => ['required', 'filled', 'alpha']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect()->back()->with($message)->withErrors($validator->errors())->withInput();
        }

        $path = $request->file('file_peminat_detail')->getRealPath();
        $columns = [
            'kode_matkul' => ord(strtolower($request->kode_matkul_column)) - 96,
            'jumlah_peminat' => ord(strtolower($request->jumlah_peminat_column)) - 96
        ];
        $peminat_detail = $this->load_data($path, $request->data_row, $columns);
        return redirect('/master/peminat/detail/' . $id . '/tambah/batch/preview')->withInput(['peminat_detail' => $peminat_detail]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        if (!$request->old('peminat_detail')) {
            return redirect('/master/peminat/detail/' . $id);
        }
        $peminat_detail = $request->old('peminat_detail');
        $peminat_detail = collect($peminat_detail);
        $peminat = $this->peminat_model->find($id);
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Preview Peminat Mata Kuliah',
            'peminat_detail' => $peminat_detail,
            'breadcrumbs' => $breadcrumbs,
            'action' => URL::to('/master/peminat/detail/' . $id . '/tambah/batch'),
            'peminat' => $peminat
        ];
        echo "<script>var peminat_detail = " . $peminat_detail->toJson() . "</script>";
        return view('peminat_detail.list_preview', $data);
    }

    public function load_data($path = "", $row, $columns)
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestRow();
        $data = [];
        for ($i = $row; $i <= $highestRow; $i++) {
            $data[] = [
                'kode_matkul' => $sheet->getCellByColumnAndRow($columns['kode_matkul'], $i)->getValue(),
                'jumlah_peminat' => $sheet->getCellByColumnAndRow($columns['jumlah_peminat'], $i)->getValue()
            ];
        }
        $data = collect($data);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        $rules = [
            'peminat_detail.*.jumlah_peminat' => ['required', 'filled', 'integer'],
            'peminat_detail.*.kode_matkul' => ['required']
        ];

        $message['peminat_detail.*.jumlah_peminat.required'] = 'Jumlah Peminat field is required.';
        $message['peminat_detail.*.kode_matkul.distinct'] = 'Kode Mata Kuliah field has a duplicate value.';
        $message['peminat_detail.*.kode_matkul.unique'] = 'Kode Mata Kuliah has already been taken.';
        $message['peminat_detail.*.kode_matkul.exists'] = 'Kode Mata Kuliah is invalid.';

        foreach ($request->peminat_detail as $index => $item) {
            $rules['peminat_detail.' . $index . '.kode_matkul'] = [
                'required', 'filled', 'distinct', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL',
                Rule::unique('peminat_detail')->where(function ($query) use ($id, $item) {
                    return $query->where('peminat_id', $id)->where('kode_matkul', $item['kode_matkul'])->where('deleted_at', NULL);
                })
            ];
        }
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $peminat = $this->peminat_model->find($id);
        $peminat_detail = $request->peminat_detail;
        foreach ($peminat_detail as $index => $item) {
            $peminat_detail[$index]['peminat_id'] = $id;
            $peminat_detail[$index]['created_at'] = new \DateTime;
        }
        try {
            $status = $peminat->peminat_detail()->insert($peminat_detail);
            if ($status) {
                $message = [
                    'success' => '<strong>Berhasil menambahkan peminat mata kuliah!</strong>'
                ];
                return redirect('/master/peminat/detail/' . $id)->with($message);
            }
        } catch (Exception $e) {
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect()->back()->with($message)->withInput();
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
    public function destroy($id)
    {
        //
    }
}
