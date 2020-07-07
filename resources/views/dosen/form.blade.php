@extends('layoutTemplate')
@section('title',$title)

@section('contentHeading')
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>Jika ingin menambahkan Mata Kuliah yang diampu, silahkan pergi ke detail Dosen</strong>
</div>

<script>
  $(".alert").alert();
</script>
@endsection

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Kode Dosen</label>
        <input type="number" class="form-control" name="kode_dosen"
        placeholder="Masukan Kode Dosen"
        @isset($dosen)
        value="{{old('kode_dosen',$dosen->kode_dosen)}}"
        @else
        value="{{old('kode_dosen')}}"
        @endisset
        >
        @if ($errors->has('kode_dosen'))
            @error('kode_dosen')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: 10</small>

        @endif
    </div>
    <div class="form-group">
        <label for="">Nama Dosen</label>
        <input type="text" class="form-control" name="nama_dosen"
        placeholder="Masukan Nama Dosen"
        @isset($dosen)
        value="{{old('nama_dosen',$dosen->nama_dosen)}}"
        @else
        value="{{old('nama_dosen')}}"
        @endisset
        >
        @if ($errors->has('nama_dosen'))
            @error('nama_dosen')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: Joko Anwar</small>

        @endif
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
