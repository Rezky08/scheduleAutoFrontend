@extends('layoutTemplate')
@section('title',$title)

@section('content')

<div class="card my-3">
    <div class="card-body">
        <form action="" method="get">
            <div class="form-group">
                <label for="">Peminat Mata Kuliah</label>
                <select class="form-control" name="peminat_id" onchange="submit()">
                    <option
                    @if (!old('peminat_id',Request::input('peminat_id')))
                    selected
                    @endif
                    >-- pilih tahun ajaran dan semester peminat --</option>
                    @foreach ($peminat as $item)
                    <option value="{{$item->id}}"
                        @if (old('peminat_id',Request::input('peminat_id')) == $item->id)
                            selected
                        @endif
                    >Tahun Ajaran {{$item->tahun_ajaran}} Semester {{$item->semester_detail->keterangan}}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if (old('peminat_id',Request::input('peminat_id')))
            <form action="{{$action}}" method="post">
                <hr>
                @csrf
                <input type="hidden" name="peminat_id" value="{{Request::input('peminat_id')}}">
                <small class="text-muted">Persyaratan</small>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Minimal Mahasiswa Ruangan Kelas</label>
                          <input type="number"
                            class="form-control" name="min_perkelas" id="" placeholder="" value="{{old('min_perkelas',20)}}">
                            @if ($errors->has('min_perkelas'))
                                @error('min_perkelas')
                                    <small class="text-danger">{{$message}}</small>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Maksimal Mahasiswa Ruangan Kelas</label>
                          <input type="number"
                        class="form-control" name="max_perkelas" id="" placeholder="" value="{{old('max_perkelas',50)}}">
                            @if ($errors->has('max_perkelas'))
                            @error('max_perkelas')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Minimal Mahasiswa Ruangan Lab</label>
                          <input type="number"
                            class="form-control" name="min_perlab" id="" placeholder="" value="{{old('min_perlab',15)}}">
                            @if ($errors->has('min_perlab'))
                            @error('min_perlab')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Maksimal Mahasiswa Ruangan Lab</label>
                          <input type="number"
                            class="form-control" name="max_perlab" id="" placeholder="" value="{{old('max_perlab',30)}}">
                            @if ($errors->has('max_perlab'))
                            @error('max_perlab')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                          <label for="">Maksimal Kelompok Dosen Mengajar</label>
                          <input type="number"
                            class="form-control" name="max_kelompok" id="" placeholder="" value="{{old('max_kelompok',3)}}">
                            @if ($errors->has('max_kelompok'))
                            @error('max_kelompok')
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
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

@if ($peminat_detail)
<div class="card my-3">
    <div class="card-header">
        Total data : {{$peminat_detail->total()}}
    </div>
    <div class="card-body">
        <h4 class="card-title">Tahun Ajaran {{$peminat_detail->first()->peminat->tahun_ajaran}} Semester {{$peminat_detail->first()->peminat->semester_detail->keterangan}}</h4>
        <table class="table table-hover">
            <thead>
                <tr align="center">
                    <th>Id</th>
                    <th>Kode Mata Kuliah</th>
                    <th>Nama Mata Kuliah</th>
                    <th>Jumlah Peminat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminat_detail as $item)
                <tr align="center">
                    <td>{{$item->id}}</td>
                    <td>{{$item->kode_matkul}}</td>
                    <td>{{$item->mata_kuliah->nama_matkul}}</td>
                    <td>{{$item->jumlah_peminat}}</td>
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
            {{$peminat_detail->links()}}
        </div>
    </div>
</div>
@endif


@endsection
