@extends('layoutTemplate')
@section('title',$title)

@section('content')
<div class="card">
    <div class="card-header bg-gradient-primary text-white">
        <span class="card-title h4">Detail Mata Kuliah {{$mata_kuliah->nama_matkul}}</span>
        <a href="{{URL::to('/master/mata-kuliah/detail/'.$mata_kuliah->id.'/dosen')}}" class="btn btn-info btn-icon-split float-right">
            <span class="icon text-white">
                <i class="fas fa-edit"></i>
            </span>
            <span class="text text-white">
                Dosen Pengampu
            </span>
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                Kode Mata Kuliah
            </div>
            <div class="col-md-3">
                : {{$mata_kuliah->kode_matkul}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                Nama Dosen
            </div>
            <div class="col-md">
                : {{$mata_kuliah->nama_matkul}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                Dosen Pengampu
            </div>
            <div class="col-md row">
                <div class="col-md-1">:</div>
                <div class="col-md">
                    @forelse ($mata_kuliah->dosen_matkul as $item)
                    <div class="row">
                        <div class="col-md">
                            <a class="text-secondary" href="{{URL::to('/master/dosen/detail/'.$item->dosen->id)}}">
                                {{$item->dosen->nama_dosen}} ({{$item->dosen->kode_dosen}})
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="row">
                        <div class="col-md">
                            <i>Tidak ada</i>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
