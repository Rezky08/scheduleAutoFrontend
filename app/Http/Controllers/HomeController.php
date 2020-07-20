<?php

namespace App\Http\Controllers;

use App\Dosen;
use App\Jadwal;
use App\Matakuliah;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $mata_kuliah_model;
    private $dosen_model;
    private $jadwal_model;
    function __construct()
    {
        $this->mata_kuliah_model = new Matakuliah();
        $this->dosen_model = new Dosen();
        $this->jadwal_model = new Jadwal();
    }
    public function index()
    {
        $mata_kuliah = $this->mata_kuliah_model->all();
        $dosen = $this->dosen_model->all();
        $jadwal = $this->jadwal_model->all();
        $data = [
            'mata_kuliah' => $mata_kuliah,
            'dosen' => $dosen,
            'jadwal' => $jadwal,
            'title' => "Dashboard"
        ];
        return view('home', $data);
    }
}
