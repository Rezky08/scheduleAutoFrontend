@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
      <label for="">Mata Kuliah</label>
      <select class="form-control selectpicker" name="kode_matkul" id="" data-live-search="true">
        <option disabled
        @if (!old('kode_matkul'))
            selected
        @endif
        > -- pilih mata kuliah -- </option>
        @foreach ($mata_kuliah as $item)
      <option value="{{$item->kode_matkul}}"
        @isset($peminat_detail)
            @if ($item->kode_matkul === $peminat_detail->kode_matkul)
                selected
            @endif
        @endisset
        @if ($item->kode_matkul === old('kode_matkul'))
            selected
        @endif
        >{{$item->nama_matkul}} - {{$item->kode_matkul}}</option>
        @endforeach
      </select>
      <small class="text-danger">
        @error('kode_matkul')
            {{$message}}
        @enderror
      </small>
    </div>
    <div class="form-group">
      <label for="">Jumlah Peminat</label>
      <input type="number"
        class="form-control" name="jumlah_peminat"
        @isset($peminat_detail)
            value="{{old('jumlah_peminat',$peminat_detail->jumlah_peminat)}}"
            @else
            value="{{old('jumlah_peminat')}}"
        @endisset
        >
        <small class="text-danger">
            @error('jumlah_peminat')
                {{$message}}
            @enderror
          </small>
    </div>
    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection

@section('script')
<script>
    $(function() {
       $('.selectpicker').selectpicker();
    });
</script>
@endsection
