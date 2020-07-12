@extends('layoutTemplate')
@section('title',$title)


@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
      <label for="">Mata Kuliah</label>
      <select data-live-search="true" class="form-control selectpicker" name="kode_matkul">
        <option
        @if (!old('kode_matkul',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kode_matkul:null))
            selected
        @endif
        > -- pilih mata kuliah -- </option>
        @foreach ($mata_kuliah as $item)
        <option value="{{$item->kode_matkul}}"
            @if ($item->kode_matkul == old('kode_matkul',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kode_matkul:null))
                selected
            @endif
            >{{$item->nama_matkul}} - {{$item->kode_matkul}}</option>
        @endforeach
      </select>
      @if ($errors->has('kode_matkul'))
          @error('kode_matkul')
            <small class="text-danger">{{$message}}</small>
          @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="">Dosen</label>
      <select data-live-search="true" class="form-control selectpicker" name="kode_dosen">
        <option
        @if (!old('kode_dosen',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kode_dosen:null))
            selected
        @endif
        > -- pilih dosen -- </option>
        @foreach ($dosen as $item)
        <option value="{{$item->kode_dosen}}"
            @if ($item->kode_dosen == old('kode_dosen',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kode_dosen:null))
                selected
            @endif
            >{{$item->nama_dosen}} ({{$item->kode_dosen}})</option>
        @endforeach

      </select>
      @if ($errors->has('kode_dosen'))
          @error('kode_dosen')
            <small class="text-danger">{{$message}}</small>
          @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="">Kelompok</label>
        <input type="text" class="form-control" name="kelompok" placeholder="Masukan nama kelompok mata kuliah" value="{{old('kelompok',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kelompok:null)}}">
        @if ($errors->has('kelompok'))
          @error('kelompok')
            <small class="text-danger">{{$message}}</small>
          @enderror
        @endif
    </div>
    <div class="form-group">
      <label for="">Kapasitas</label>
        <input type="number" class="form-control" name="kapasitas" placeholder="Masukan kapasitas mahasiswa" value="{{old('kapasitas',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kapasitas:null)}}">
        @if ($errors->has('kapasitas'))
          @error('kapasitas')
            <small class="text-danger">{{$message}}</small>
          @enderror
        @endif
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection

@section('script')
<script>
    function deleteForm(selector) {
        let parent =$(selector).closest("#rowForm");
        $(parent).remove();
     }
</script>
@endsection
