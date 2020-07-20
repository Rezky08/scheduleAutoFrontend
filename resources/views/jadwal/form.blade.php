@extends('layoutTemplate')
@section('title',$title)

@section('content')

<div class="card my-3">
    <div class="card-body">
        <form action="" method="get">
            <div class="form-group">
                <label for="">Kelompok Dosen</label>
                <select  class="form-control" name="kelompok_dosen_id" onchange="submit()">
                    <option
                    @if (!old('kelompok_dosen_id',Request::input('kelompok_dosen_id')))
                    selected
                    @endif
                    >-- pilih Kelompok Dosen --</option>
                    @foreach ($kelompok_dosen as $item)
                    <option value="{{$item->id}}"
                        @if (old('kelompok_dosen_id',Request::input('kelompok_dosen_id')) == $item->id)
                            selected
                        @endif
                    > Id {{$item->id}} - Tahun Ajaran {{$item->peminat->tahun_ajaran}} Semester {{$item->peminat->semester_detail->keterangan}}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if (old('kelompok_dosen_id',Request::input('kelompok_dosen_id')))
            <form action="{{$action}}" method="post">
                <hr>
                @csrf
                <input type="hidden" name="kelompok_dosen_id" value="{{Request::input('kelompok_dosen_id')}}">
                <small class="text-muted">Properti Algoritma Genetika</small>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Crossover Rate (%)</label>
                          <input type="number"
                            class="form-control" name="crossover_rate" placeholder="example : 0.75" value="{{old('crossover_rate')}}" min="0.1" max="1.0" step="0.01">
                            @if ($errors->has('crossover_rate'))
                            @error('crossover_rate')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Mutation Rate (%)</label>
                          <input type="number"
                            class="form-control" name="mutation_rate" placeholder="example : 0.5" value="{{old('mutation_rate')}}" min="0.1" max="1.0" step="0.01">
                            @if ($errors->has('mutation_rate'))
                            @error('mutation_rate')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Jumlah Generasi</label>
                          <input type="number"
                            class="form-control" name="num_generation" placeholder="example : 10" value="{{old('num_generation')}}">
                            @if ($errors->has('num_generation'))
                            @error('num_generation')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Jumlah Populasi</label>
                          <input type="number"
                            class="form-control" name="num_population" placeholder="example : 20" value="{{old('num_population')}}">
                            @if ($errors->has('num_population'))
                            @error('num_population')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary float-right mb-3">Simpan</button>
            </form>
        @endif
    </div>
</div>

@if ($kelompok_dosen_detail)
<div class="card my-3">
    <div class="card-header">
        Total data : {{$kelompok_dosen_detail->total()}}
    </div>
    <div class="card-body">
        <h4 class="card-title">Tahun Ajaran {{$kelompok_dosen_select->peminat->tahun_ajaran}} Semester {{$kelompok_dosen_select->peminat->semester_detail->keterangan}}</h4>
        <table class="table table-hover">
            <thead>
                <tr align="center">
                    <th>Id</th>
                    <th>Nama Dosen</th>
                    <th>Nama Mata Kuliah</th>
                    <th>Kelompok</th>
                    <th>Kapasitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelompok_dosen_detail as $item)
                <tr align="center">
                    <td>{{$item->id}}</td>
                    <td>{{$item->dosen->nama_dosen}}</td>
                    <td>{{$item->mata_kuliah->nama_matkul}}</td>
                    <td>{{$item->kelompok}}</td>
                    <td>{{$item->kapasitas}}</td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="text-center"><strong>Data Tidak Ada</strong></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer text-muted row justify-content-center">
        <div class="col-md-6">
            {{$kelompok_dosen_detail->links()}}
        </div>
    </div>
</div>
@endif


@endsection
