@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Kode Program Studi</label>
        <input type="text" class="form-control" name="kode_prodi"
        placeholder="Masukan Kode Program Studi"
        @isset($program_studi)
        value="{{old('kode_prodi',$program_studi->kode_prodi)}}"
        @else
        value="{{old('kode_prodi')}}"
        @endisset
        >
        @if ($errors->has('kode_prodi'))
            @error('kode_prodi')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: TI</small>

        @endif
    </div>
    <div class="form-group">
        <label for="">Nama Program Studi</label>
        <input type="text" class="form-control" name="nama_prodi" placeholder="Masukan Nama Program Studi"
        @isset($program_studi)
        value="{{old('nama_prodi',$program_studi->nama_prodi)}}"
        @else
        value="{{old('nama_prodi')}}"
        @endisset
        >
        @if ($errors->has('nama_prodi'))
        @error('nama_prodi')
            <small class="form-text text-danger">{{$message}}</small>
        @enderror
        @else
            <small class="form-text text-muted">Contoh: Teknik Informatika</small>

    @endif
    </div>
    <div class="form-group">
      <label for="">Keterangan</label>
      <textarea class="form-control" name="keterangan"rows="3">
        @isset($program_studi)
        {{old('keterangan',$program_studi->keterangan_prodi)}}
        @else
        {{old('keterangan')}}
        @endisset
      </textarea>
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
