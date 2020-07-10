<?php

namespace App\Http\Controllers;

use App\DosenMatakuliah;
use App\Events\AlgenKelompokDosenEvent;
use App\Events\AlgenKelompokDosenQueEvent;
use App\Events\UpdateStatusKelompokDosenEvent;
use App\KelompokDosen;
use App\Peminat;
use App\PeminatDetail;
use App\ProcessLog;
use App\SemesterDetail;
use BreadCrumbs;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class KelompokDosenController extends Controller
{
    private $breadcrumbs_helper;
    private $peminat_model;
    private $peminat_detail_model;
    private $semester_model;
    private $kelompok_dosen_model;
    private $dosen_mata_kuliah_model;
    private $guzzl_request;
    private $python_url;
    private $process_log_model;

    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->peminat_model = new Peminat();
        $this->peminat_detail_model = new PeminatDetail();
        $this->semester_model = new SemesterDetail();
        $this->kelompok_dosen_model = new KelompokDosen();
        $this->guzzl_request = new Client();
        $this->dosen_mata_kuliah_model = new DosenMatakuliah();
        $this->process_log_model = new ProcessLog();
        $this->python_url = "http://localhost:5000";
        $process_log = $this->process_log_model->unfinished()->get();
        $process_id = $process_log->pluck('id')->toArray();
        event(new UpdateStatusKelompokDosenEvent($process_id));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->searchbox) {
            $kelompok_dosen = $this->search($request->searchbox);
        } else {
            $kelompok_dosen = $this->kelompok_dosen_model;
        }
        $kelompok_dosen = $kelompok_dosen->paginate(15);
        $kelompok_dosen->appends($request->all())->render();
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Kelompok Dosen',
            'breadcrumbs' => $breadcrumbs,
            'kelompok_dosen' => $kelompok_dosen
        ];

        echo "<script>var kelompok_dosen = " . $data['kelompok_dosen']->toJson() . "</script>";

        return view('kelompok_dosen.list', $data);
    }


    public function search($string = "")
    {
        $this->kelompok_dosen->leftJoin('peminat', 'kelompok_dosen.peminat_id', '=', 'peminat.id');
        $kelompok_dosen = $this->kelompok_dosen_model->where('peminat.tahun_ajaran', 'like', '%' . $string . '%')->orwhere('peminat.semester', 'like', '%' . $string . '%');
        return $kelompok_dosen;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $peminat = $this->peminat_model->all();
        $peminat_detail = [];
        if ($request->peminat_id) {
            $peminat_detail = $this->peminat_detail_model->where('peminat_id', $request->peminat_id);
            $peminat_detail = $peminat_detail->paginate(15);
            $peminat_detail->appends($request->all())->render();
        }
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Tambah Kelompok Dosen',
            'breadcrumbs' => $breadcrumbs,
            'peminat' => $peminat,
            'peminat_detail' => $peminat_detail,
            'action' => URL::to('/penjadwalan/kelompok-dosen/tambah')
        ];
        return view('kelompok_dosen.form', $data);
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
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL'],
            'min_perkelas' => ['required', 'integer', 'min:1'],
            'max_perkelas' => ['required', 'integer', 'min:1'],
            'min_perlab' => ['required', 'integer', 'min:1'],
            'max_perlab' => ['required', 'integer', 'min:1'],
            'max_kelompok' => ['required', 'integer', 'min:1'],
            'crossover_rate' => ['required', 'regex:/^(?:0*(?:\.\d+)?|1(\.0*)?)$/'],
            'mutation_rate' => ['required', 'regex:/^(?:0*(?:\.\d+)?|1(\.0*)?)$/'],
            'num_generation' => ['required', 'min:1'],
            'num_population' => ['required', 'min:1'],
        ];
        $message = [
            'crossover_rate.regex' => "Nilai :attribute antara 0.1 sampai 1.0",
            'mutation_rate.regex' => "Nilai :attribute antara 0.1 sampai 1.0"
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $peminat_detail = $this->peminat_model->find($request->peminat_id)->peminat_detail;
        $peminat_detail = $peminat_detail->map(function ($item, $index) {
            $merged = collect($item->toArray());
            $merged = $merged->merge($item->mata_kuliah->toArray());
            return $merged->except('id')->toArray();
        });
        $dosen_mata_kuliah = $this->dosen_mata_kuliah_model->all();
        $dosen_mata_kuliah = $dosen_mata_kuliah->groupBy('kode_matkul');
        $dosen_mata_kuliah = $dosen_mata_kuliah->map(function ($item, $key) {
            return [
                'kode_matkul' => $key,
                'kode_dosen' => $item->pluck('kode_dosen')->toArray()
            ];
        })->values();

        // create pembagian mata kuliah params
        $kelompok_mata_kuliah_params = [
            'peminat_params' => $peminat_detail->toArray(),
            'peminat_props' => [
                'min_perkelas' => $request->min_perkelas,
                'max_perkelas' => $request->max_perkelas,
                'min_perlab' => $request->min_perlab,
                'max_perlab' => $request->max_perlab
            ]
        ];
        foreach ($kelompok_mata_kuliah_params['peminat_props'] as $key => $value) {
            $kelompok_mata_kuliah_params['peminat_props'][$key] = (int) $value;
        }

        // algen kelompok dosen params
        $algen_params = [
            'nn_params' => [
                'matkul_dosen' => $dosen_mata_kuliah->toArray()
            ],
            'rules' => [
                'max_kelompok' => (int) $request->max_kelompok
            ],
            'num_generation' => (int) $request->num_generation,
            'num_population' => (int) $request->num_population,
            'crossover_rate' => floatval($request->crossover_rate),
            'mutation_rate' => floatval($request->mutation_rate),
            'timeout' => 0
        ];
        $params = $algen_params + $kelompok_mata_kuliah_params;

        // insert kelompok dosen
        try {
            $url = $this->python_url . "/dosen";
            $res = $this->guzzl_request->post($url, ['json' => $params]);
            $kelompok_dosen_id = null;
            if ($res->getStatusCode() == 200) {
                $data_insert = [
                    'peminat_id' => $request->peminat_id,
                    'created_at' => new \DateTime
                ];
                try {
                    $kelompok_dosen_id = $this->kelompok_dosen_model->insertGetId($data_insert);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    $message = [
                        'error' => $e->getMessage()
                    ];
                    return redirect()->back()->with($message)->withInput();
                }
            }
            $res = $res->getBody()->getContents();
            $res = json_decode($res);

            // insert process_log
            try {
                $data_insert = [
                    'process_item_id' => 1,
                    'item_key' => $kelompok_dosen_id,
                    'celery_id' => $res->celery_id,
                    'created_at' => new \DateTime
                ];
                $process_id = $this->process_log_model->insertGetId($data_insert);

                Log::info('Send to Python Enginge ' . date('Y-m-d H:i:s'));
                event(new AlgenKelompokDosenQueEvent($process_id));
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $message = [
                    'error' => $e->getMessage()
                ];
                return redirect()->back()->with($message)->withInput();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = [
                'error' => $e->getMessage()
            ];
            return redirect()->back()->with($message)->withInput();
        }

        $message = [
            'success' => "<strong>Proses Sedang Berjalan</strong>"
        ];
        return redirect('/penjadwalan/kelompok-dosen')->with($message);
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
