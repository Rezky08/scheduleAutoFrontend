<?php

namespace App\Http\Controllers;

use App\Events\AlgenJadwalEvent;
use App\Events\AlgenJadwalQueEvent;
use App\Events\UpdateStatusJadwalEvent;
use App\Exports\ExportJadwal;
use App\Hari;
use App\Jadwal;
use App\JadwalDetail;
use App\KelompokDosen;
use App\ProcessLog;
use App\ProcessParam;
use App\Ruang;
use App\Sesi;
use BreadCrumbs;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JadwalController extends Controller
{
    private $breadcrumbs_helper;
    private $jadwal_model;
    private $jadwal_detail_model;
    private $kelompok_dosen_model;
    private $ruang_model;
    private $sesi_model;
    private $hari_model;
    private $guzzle_request;
    private $python_url;
    private $process_log_model;
    private $process_param_model;
    function __construct()
    {
        $this->breadcrumbs_helper = new BreadCrumbs();
        $this->jadwal_model = new Jadwal();
        $this->jadwal_detail_model = new JadwalDetail();
        $this->kelompok_dosen_model = new KelompokDosen();
        $this->ruang_model = new Ruang();
        $this->sesi_model = new Sesi();
        $this->hari_model = new Hari();
        $this->guzzle_request = new Client();
        $this->python_url = "http://localhost:5000";
        $this->process_log_model = new ProcessLog();
        $this->process_param_model = new ProcessParam();
        $process_log = $this->process_log_model->unfinished(2)->get();
        $process_id = $process_log->pluck('id')->toArray();
        event(new UpdateStatusJadwalEvent($process_id));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->searchbox) {
            $jadwal = $this->search($request->searchbox);
        } else {
            $jadwal = $this->jadwal_model;
        }
        $jadwal = $jadwal->paginate(15);
        $jadwal->appends($request->all())->render();
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Jadwal',
            'breadcrumbs' => $breadcrumbs,
            'jadwal' => $jadwal
        ];

        echo "<script>var jadwal = " . $data['jadwal']->toJson() . "</script>";
        return view('jadwal.list', $data);
    }

    public function search($string = "")
    {
        $jadwal = $this->jadwal_model->where('tahun_ajaran', 'like', '%' . $string . '%')->orwhere('semester', 'like', '%' . $string . '%');
        return $jadwal;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $kelompok_dosen = $this->kelompok_dosen_model->all();
        $kelompok_dosen_detail = [];
        $kelompok_dosen_select = null;
        if ($request->kelompok_dosen_id) {
            $kelompok_dosen_select = $kelompok_dosen->find($request->kelompok_dosen_id);
            $kelompok_dosen_detail = $kelompok_dosen_select->detail();
            $kelompok_dosen_detail = $kelompok_dosen_detail->paginate(15);
            $kelompok_dosen_detail->appends($request->all())->render();
        }
        $breadcrumbs = $request->segments();
        $breadcrumbs = $this->breadcrumbs_helper->make($breadcrumbs);
        $data = [
            'title' => 'Tambah Kelompok Dosen',
            'breadcrumbs' => $breadcrumbs,
            'kelompok_dosen' => $kelompok_dosen,
            'kelompok_dosen_select' => $kelompok_dosen_select,
            'kelompok_dosen_detail' => $kelompok_dosen_detail,
            'action' => URL::to('/penjadwalan/jadwal/tambah')
        ];
        return view('jadwal.form', $data);
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
            'crossover_rate' => ['required', 'regex:/^(?:0*(?:\.\d+)?|1(\.0*)?)$/'],
            'mutation_rate' => ['required', 'regex:/^(?:0*(?:\.\d+)?|1(\.0*)?)$/'],
            'num_generation' => ['required', 'min:1'],
            'num_population' => ['required', 'min:1'],
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'timeout' => ['sometimes', 'integer']
        ];
        $message = [
            'crossover_rate.regex' => "Nilai :attribute antara 0.1 sampai 1.0",
            'mutation_rate.regex' => "Nilai :attribute antara 0.1 sampai 1.0"
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $kelompok_dosen = $this->kelompok_dosen_model->find($request->kelompok_dosen_id);
        $kelompok_dosen_detail = $kelompok_dosen->detail;
        $kelompok_dosen_detail = $kelompok_dosen_detail->map(function ($item) {
            $merged = collect($item->toArray());
            $mata_kuliah = collect($item->mata_kuliah)->except('id')->toArray();
            $dosen = collect($item->dosen)->except('id')->toArray();
            $merged = $merged->merge($mata_kuliah);
            $merged = $merged->merge($dosen);
            return $merged->toArray();
        });

        $ruang = $this->ruang_model->all();
        $ruang = $ruang->map(function ($item) {
            $item = collect($item->toArray());
            return $item->except('id')->toArray();
        });

        $sesi = $this->sesi_model->all();
        $sesi = $sesi->map(function ($item) {
            $item = collect($item->toArray());
            return $item->except('id')->toArray();
        });

        $hari = $this->hari_model->all();

        $params = [
            'nn_params' => [
                'ruang' => $ruang->toArray(),
                'sesi' => $sesi->toArray(),
                'hari' => $hari->pluck('nama_hari')->toArray(),
                'mata_kuliah' => $kelompok_dosen_detail->toArray()
            ],
            'num_generation' => (int) $request->num_generation,
            'num_population' => (int) $request->num_population,
            'crossover_rate' => floatval($request->crossover_rate),
            'mutation_rate' => floatval($request->mutation_rate),
            'timeout' => $request->timeout ? $request->timeout : 0,
        ];

        // insert kelompok dosen
        try {
            $url = $this->python_url . "/jadwal";
            $res = $this->guzzle_request->post($url, ['json' => $params]);
            $jadwal_id = null;
            if ($res->getStatusCode() == 200) {
                $data_insert = [
                    'tahun_ajaran' => $kelompok_dosen->peminat->tahun_ajaran,
                    'semester' => $kelompok_dosen->peminat->semester,
                    'created_at' => new \DateTime
                ];
                try {
                    $jadwal_id = $this->jadwal_model->insertGetId($data_insert);
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
                    'process_item_id' => 2,
                    'item_key' => $jadwal_id,
                    'celery_id' => $res->celery_id,
                    'created_at' => new \DateTime
                ];
                $process_id = $this->process_log_model->insertGetId($data_insert);

                Log::info('Send to Python Enginge ' . date('Y-m-d H:i:s'));
                $data_insert = [
                    'process_log_id' => $process_id,
                    'parameters' => json_encode($params),
                    'created_at' => new \DateTime
                ];
                $process_params = $this->process_param_model->insert($data_insert);

                event(new AlgenJadwalQueEvent($process_id));
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
        return redirect('/penjadwalan/jadwal')->with($message);
    }

    public function download($id)
    {
        $rules = ['id' => ['required', 'exists:jadwal,id,deleted_at,NULL']];
        $validator = Validator::make(['id' => $id], $rules);
        if ($validator->fails()) {
            $message = [
                'error' => $validator->errors()->first()
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }

        $jadwal = $this->jadwal_model->find($id);
        $jadwal_detail = $jadwal->jadwal_detail;
        if (!$jadwal_detail) {
            $message = [
                'error' => "Tidak Ada Jadwal Detail"
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }
        $tahun_ajaran = preg_replace("/[^a-zA-Z0-9]+/", "", $jadwal->tahun_ajaran);
        $filename = $jadwal->id . $jadwal->semester_detail->keterangan . $tahun_ajaran  . date('ymd');

        $jadwal_export = new ExportJadwal($jadwal->id);
        return Excel::download($jadwal_export, $filename . '.xlsx');
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
        $jadwal = $this->jadwal_model->find($id);
        if (!$jadwal) {
            $message = [
                'error' => "<strong>Jadwal Tidak Ditemukan!</strong>"
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }

        $status = $jadwal->delete();
        if ($status) {
            $message = [
                'success' => "<strong>'Berhasil Hapus Jadwal!'</strong>" . 'Jadwal ' . $jadwal->id . ' telah dihapus.'
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        } else {
            $message = [
                'error' => 'Gagal Hapus Jadwal !'
            ];
            return redirect('/penjadwalan/jadwal')->with($message);
        }
    }
}
