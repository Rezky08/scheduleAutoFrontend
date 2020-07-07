@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Nama Ruangan</label>
        <input type="text" class="form-control" name="nama_ruang"
        placeholder="Masukan Nama Ruangan"
        @isset($ruang)
        value="{{old('nama_ruang',$ruang->nama_ruang)}}"
        @else
        value="{{old('nama_ruang')}}"
        @endisset
        >
        @if ($errors->has('nama_ruang'))
            @error('nama_ruang')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: 7.4.1</small>

        @endif
    </div>
    <div class="form-group">
        <label for="">Kapasitas Ruang</label>
        <input type="number" class="form-control" name="kapasitas" placeholder="Masukan Kapasitas Ruang"
        @isset($ruang)
        value="{{old('kapasitas',$ruang->kapasitas)}}"
        @else
        value="{{old('kapasitas')}}"
        @endisset
        >
        @if ($errors->has('kapasitas'))
        @error('kapasitas')
            <small class="form-text text-danger">{{$message}}</small>
        @enderror
        @else
            <small class="form-text text-muted">Contoh: 30</small>

        @endif
    </div>
    <div class="form-group">
      <label for="">Keterangan</label>
      <textarea class="form-control" name="keterangan"rows="3">
        @isset($ruang)
        {{old('keterangan',$ruang->keterangan)}}
        @else
        {{old('keterangan')}}
        @endisset
      </textarea>
      @if ($errors->has('keterangan'))
      @error('keterangan')
          <small class="form-text text-danger">{{$message}}</small>
      @enderror
      @endif
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
