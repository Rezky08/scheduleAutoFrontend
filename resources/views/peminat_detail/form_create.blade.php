@extends('layoutTemplate')
@section('title',$title)

@section('content')
<div class="card container-fluid">
  <div class="card-header bg-white row">
    <div class="col-md-6">
        <span class="h3">Peminat Mata Kuliah</span>
    </div>
    <div class="col-md-6">
        <form action="" method="get" class="row justify-content-end">
            <div class="form-group">
              <input type="number" class="form-control" name="num_form" placeholder="jumlah data">
            </div>
            <input type="hidden" name="last_num_form" value="{{$num_form}}">
            <button type="submit" class="btn btn-info float-right mb-3 mx-2">Tambah +</button>
        </form>
    </div>
  </div>
  <div class="card-body">
    <form action="{{$action}}" method="post">
        @csrf
        @for ($i = 0; $i < $num_form; $i++)
            <div class="row" id="rowForm">
                <div class="form-group col-md-6">
                <select class="form-control selectpicker" name="peminat_detail[{{$i}}][kode_matkul]" data-live-search="true">
                    <option disabled
                    @if (!old('peminat_detail.'.$i.'.kode_matkul'))
                        selected
                    @endif
                    > -- pilih mata kuliah -- </option>
                    @foreach ($mata_kuliah as $item)
                <option value="{{$item->kode_matkul}}"
                    @if ($item->kode_matkul === old('peminat_detail.'.$i.'.kode_matkul'))
                        selected
                    @endif
                    >{{$item->nama_matkul}} - {{$item->kode_matkul}}</option>
                    @endforeach
                </select>
                <small class="text-danger">
                    @error('peminat_detail.'.$i.'.kode_matkul')
                        {{$message}}
                    @enderror
                </small>
                </div>
                <div class="form-group col-md-5">
                <input type="number"
                    class="form-control" name="peminat_detail[{{$i}}][jumlah_peminat]" placeholder="jumlah peminat"  value="{{old('peminat_detail.'.$i.'.jumlah_peminat')}}"
                    >
                    <small class="text-danger">
                        @error('peminat_detail.'.$i.'.jumlah_peminat')
                            {{$message}}
                        @enderror
                    </small>
                </div>
                <div class="col-md text-center">
                    <button class="btn btn-danger" type="button" onclick="deleteForm(this)"><i class="fas fa-trash"></i></button>
                </div>
            </div>

        @endfor
        <button class="btn btn-primary float-right mb-3">Simpan</button>
    </form>

  </div>

</div>

@endsection

@section('script')
<script>
    $(function() {
       $('.selectpicker').selectpicker();
    });
    function deleteForm(selector) {
        let parent =$(selector).closest("#rowForm");
        $(parent).remove();
     }
</script>
@endsection
