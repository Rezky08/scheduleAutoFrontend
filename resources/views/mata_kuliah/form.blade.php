@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf

    <div class="form-group">
        <label for="">Kode Mata Kuliah</label>
        <input type="text" class="form-control" name="kode_matkul"
        placeholder="Masukan Kode Mata Kuliah"
        @isset($mata_kuliah)
        value="{{old('kode_matkul',$mata_kuliah->kode_matkul)}}"
        @else
        value="{{old('kode_matkul')}}"
        @endisset
        >
        @if ($errors->has('kode_matkul'))
            @error('kode_matkul')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Contoh: KP002</small>

        @endif
    </div>

    <div class="form-group">
        <label for="">Nama Mata Kuliah</label>
        <input type="text" class="form-control" name="nama_matkul" placeholder="Masukan Nama Mata Kuliah"
        @isset($mata_kuliah)
        value="{{old('nama_matkul',$mata_kuliah->nama_matkul)}}"
        @else
        value="{{old('nama_matkul')}}"
        @endisset
        >
        @if ($errors->has('nama_matkul'))
        @error('nama_matkul')
            <small class="form-text text-danger">{{$message}}</small>
        @enderror
        @else
            <small class="form-text text-muted">Contoh: Algoritma dan Struktur Data 1</small>

        @endif
    </div>

    <div class="form-group">
      <label for="">SKS</label>
      <input type="number" class="form-control" name="sks_matkul" min="1" placeholder="Masukan Jumlah SKS Mata Kuliah"
      @isset($mata_kuliah)
      value="{{old('sks_matkul',$mata_kuliah->sks_matkul)}}"
      @else
      value="{{old('sks_matkul')}}"
      @endisset
      >
      @if ($errors->has('sks_matkul'))
      @error('sks_matkul')
          <small class="form-text text-danger">{{$message}}</small>
      @enderror
      @else
          <small class="form-text text-muted">SKS minimal 1</small>

      @endif
    </div>

    <div class="form-group">
      <label for="">Status Mata Kuliah</label>
      <select class="form-control" name="status_matkul" id="">
        <option value=""
        @isset($mata_kuliah)
        @else
            @if (!old('status_matkul'))
                selected
            @endif
        @endisset
        > -- Pilih Status Mata Kuliah --</option>
        <option value="1"
        @isset($mata_kuliah)
            @if ($mata_kuliah['status_matkul']===1)
                selected
            @endif
        @endisset
        @if (old('status_matkul')===1)
        selected
        @endif
        >Aktif</option>
        <option value="0"
        @isset($mata_kuliah)
        @if ($mata_kuliah['status_matkul']===0)
            selected
        @endif
        @endisset
        @if (old('status_matkul')===0)
            selected
        @endif
        >Tidak Aktif</option>
      </select>
      @if ($errors->has('status_matkul'))
      @error('status_matkul')
          <small class="form-text text-danger">{{$message}}</small>
      @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="">Tipe Ruangan</label>
      <select class="form-control" name="lab_matkul" id="">
        <option value=""
        @isset($mata_kuliah)
        @else
            @if (!old('lab_matkul'))
                selected
            @endif
        @endisset
        > -- Pilih Tipe Ruang --</option>
        <option value="1"
        @isset($mata_kuliah)
            @if ($mata_kuliah['lab_matkul']===1)
                selected
            @endif
        @endisset
        @if (old('lab_matkul')===1)
        selected
        @endif
        >LAB</option>
        <option value="0"
        @isset($mata_kuliah)
        @if ($mata_kuliah['lab_matkul']===0)
            selected
        @endif
        @endisset
        @if (old('lab_matkul')===0)
            selected
        @endif
        >KELAS</option>
      </select>
      @if ($errors->has('lab_matkul'))
      @error('lab_matkul')
          <small class="form-text text-danger">{{$message}}</small>
      @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="">Program Studi</label>
      <select class="form-control" name="kode_prodi" id="">
        <option
        @isset($mata_kuliah)
            @else
            @if (!old('kode_prodi'))
                selected
            @endif
        @endisset
        > -- Pilih Program Studi Mata Kuliah -- </option>
        @forelse ($program_studi as $item)
            <option value="{{$item['kode_prodi']}}"
            @isset($mata_kuliah)
                @if ($mata_kuliah['kode_prodi'] === $item['kode_prodi'])
                    selected
                @endif
            @endisset
            @if (old('kode_prodi')===$item['kode_prodi'])
                selected
            @endif
            >{{$item['kode_prodi']}} - {{$item['nama_prodi']}}</option>
        @empty

        @endforelse
      </select>
      @if ($errors->has('kode_prodi'))
      @error('kode_prodi')
          <small class="form-text text-danger">{{$message}}</small>
      @enderror
      @else
          <small class="form-text text-muted">Contoh: TI</small>

      @endif
    </div>

    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
