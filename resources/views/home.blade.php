@extends('layoutTemplate')

@section('title',$title)
@section('contentTitle',$title)

@section('content')
<div class="row">

    <!-- Mata Kuliah Aktif Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Mata Kuliah Aktif
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>

    <!-- Dosen Aktif Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Dosen Aktif
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>

    <!-- Peminat Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Peminat
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>

    <!-- Mata Kuliah Tidak Aktif Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Mata Kuliah Tidak Aktif
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>

    <!-- Dosen Tidak Aktif Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Dosen Tidak Aktif
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                    Jadwal
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    120
                </div>
                </div>
                {{-- <div class="col-auto">
                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div> --}}
            </div>
            </div>
        </div>
    </div>


</div>
@endsection
