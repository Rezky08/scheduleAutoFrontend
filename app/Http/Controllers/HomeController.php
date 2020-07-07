<?php

namespace App\Http\Controllers;

use App\Matakuliah;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $mata_kuliah_model;
    function __construct()
    {
        $this->mata_kuliah_model = new Matakuliah();
    }
    public function index()
    {
        $data = [
            'title' => 'Menu Utama'
        ];
        return view('home', $data);
    }
}
