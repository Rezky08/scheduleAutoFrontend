@extends('layoutTemplate')
@section('title',$title)


@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <input type="hidden" name="id_jadwal" value="{{$jadwal->id}}">
    <div class="form-group">
      <label for="">Mata Kuliah</label>
      <select data-live-search="true" class="form-control selectpicker" name="kode_matkul" onchange="fillSelectInput(this)">
        <option
        @if (!old('kode_matkul',isset($jadwal_detail)?$jadwal_detail->kode_matkul:null))
            selected
        @endif
        > -- pilih mata kuliah -- </option>
        @foreach ($mata_kuliah as $item)
        <option value="{{$item->kode_matkul}}"
            @if ($item->kode_matkul == old('kode_matkul',isset($jadwal_detail)?$jadwal_detail->kode_matkul:null))
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
      <select data-live-search="true" class="form-control selectpicker" name="kode_dosen" id="selectedTarget">
        <option> -- pilih dosen -- </option>
        {{-- @foreach ($dosen as $item)
        <option value="{{$item->kode_dosen}}"
            @if ($item->kode_dosen == old('kode_dosen',isset($kelompok_dosen_detail)?$kelompok_dosen_detail->kode_dosen:null))
                selected
            @endif
            >{{$item->nama_dosen}} ({{$item->kode_dosen}})</option>
        @endforeach --}}

      </select>
      @if ($errors->has('kode_dosen'))
          @error('kode_dosen')
            <small class="text-danger">{{$message}}</small>
          @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="">Kelompok</label>
        <input type="text" class="form-control" name="kelompok" placeholder="Masukan nama kelompok mata kuliah" value="{{old('kelompok',isset($jadwal_detail)?$jadwal_detail->kelompok:null)}}">
        @if ($errors->has('kelompok'))
          @error('kelompok')
            <small class="text-danger">{{$message}}</small>
          @enderror
        @endif
    </div>
    <div class="form-group">
      <label for="">Kapasitas</label>
        <input type="number" class="form-control" name="kapasitas" placeholder="Masukan kapasitas mahasiswa" value="{{old('kapasitas',isset($jadwal_detail)?$jadwal_detail->kapasitas:null)}}">
        @if ($errors->has('kapasitas'))
          @error('kapasitas')
            <small class="text-danger">{{$message}}</small>
          @enderror
        @endif
    </div>

    <div class="form-group">
        <label for="">Hari</label>
        <select data-live-search="true" class="form-control selectpicker" name="hari">
            <option
            @if (!old('hari',isset($jadwal_detail)?$jadwal_detail->hari:null))
                selected
            @endif
            > -- pilih Hari -- </option>
            @foreach ($hari as $item)
            <option value="{{$item->id}}"
                @if ($item->nama_hari == old('hari',isset($jadwal_detail)?$jadwal_detail->hari:null))
                    selected
                @endif
                >{{$item->nama_hari}}</option>
            @endforeach
        </select>
        @if ($errors->has('hari'))
            @error('hari')
                <small class="text-danger">{{$message}}</small>
            @enderror
        @endif
    </div>

    <div class="form-group">
        <label for="">Ruang</label>
        <select data-live-search="true" class="form-control selectpicker" name="ruang">
            <option
            @if (!old('ruang',isset($jadwal_detail)?$jadwal_detail->ruang:null))
                selected
            @endif
            > -- pilih Ruang -- </option>
            @foreach ($ruang as $item)
            <option value="{{$item->id}}"
                @if ($item->nama_ruang == old('ruang',isset($jadwal_detail)?$jadwal_detail->ruang:null))
                    selected
                @endif
                >{{$item->nama_ruang}}</option>
            @endforeach
        </select>
        @if ($errors->has('ruang'))
            @error('ruang')
                <small class="text-danger">{{$message}}</small>
            @enderror
        @endif
    </div>

    <div class="form-group">
        <label for="">Sesi</label>
        <select data-live-search="true" class="form-control selectpicker" name="sesi">
            <option
            @if (!old('sesi',isset($jadwal_detail)?$jadwal_detail->sesi:null))
                selected
            @endif
            > -- pilih Sesi -- </option>
            @foreach ($sesi as $item)
            <option value="{{$item->id}}"
                @if ($item->sesi_mulai == old('sesi',isset($jadwal_detail)?$jadwal_detail->sesi_mulai:null))
                    selected
                @endif
                >{{$item->sesi_mulai}}</option>
            @endforeach
        </select>
        @if ($errors->has('sesi'))
            @error('sesi')
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
     function fillSelectInput(selector) {
        let kode_matkul = $(selector).val();
        let dosen_matkul_select =  dosen_mata_kuliah[kode_matkul];
        let html_text = "";
        html_text+="<option> -- pilih dosen -- </option>";
        $.each(dosen_matkul_select, function (indexInArray, element) {
            html_text += "<option value='"+element.kode_dosen+"'>"+element.nama_dosen+" ("+element.kode_dosen+")</option>\n";
        });
        console.log(html_text);
        // $(html_text).appendTo("#selectedTarget");
        $("#selectedTarget").html(html_text);
        $(".selectpicker").selectpicker('refresh');
      }
</script>

@endsection
