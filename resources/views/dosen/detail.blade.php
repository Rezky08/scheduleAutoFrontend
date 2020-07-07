@extends('layoutTemplate')
@section('title',$title)

@section('content')
<div class="card">
    <div class="card-header bg-gradient-primary text-white">
        <span class="card-title h4">Detail Dosen {{$dosen->nama_dosen}}</span>
        <a href="{{URL::to('/master/dosen/detail/'.$dosen->id.'/mata-kuliah')}}" class="btn btn-info btn-icon-split float-right">
            <span class="icon text-white">
                <i class="fas fa-edit"></i>
            </span>
            <span class="text text-white">
                Ampu Mata Kuliah
            </span>
        </a>
    </div>
  <div class="card-body">
      <div class="row">
          <div class="col-md-3">
              Kode
          </div>
          <div class="col-md-3">
              : {{$dosen->kode_dosen}}
          </div>
      </div>
      <div class="row">
          <div class="col-md-3">
              Nama Dosen
          </div>
          <div class="col-md">
              : {{$dosen->nama_dosen}}
          </div>
      </div>
      <div class="row">
          <div class="col-md-3">
              Mata Kuliah yang diampu
          </div>
          <div class="col-md row">
              <div class="col-md-1">
                  :
              </div>
              <div class="col-md">
              @forelse ($dosen->dosen_matkul as $item)
                <div class="row">
                    <div class="col-md">
                        <a class="text-secondary" href="{{URL::to('/master/mata-kuliah/detail/'.$item->matkul->id)}}">
                            {{$item->matkul->nama_matkul}} ({{$item->matkul->kode_matkul}})
                        </a>
                    </div>
                </div>
                @empty
                    <i>Tidak ada</i>
                    @endforelse
              </div>
            </div>
      </div>
  </div>
</div>
@endsection
