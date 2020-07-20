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
                    Mata Kuliah
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{$mata_kuliah->count()}}
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
                    Dosen
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{$dosen->count()}}
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
                    Jadwal
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{$jadwal->count()}}
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
