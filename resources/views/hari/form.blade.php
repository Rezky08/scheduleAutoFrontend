@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Nama Hari</label>
        <input type="text" class="form-control" name="nama_hari"
        placeholder="Masukan Nama Hari"
        @isset($hari)
        value="{{old('nama_hari',$hari->nama_hari)}}"
        @else
        value="{{old('nama_hari')}}"
        @endisset
        >
        @if ($errors->has('nama_hari'))
            @error('nama_hari')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: Senin</small>

        @endif
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
