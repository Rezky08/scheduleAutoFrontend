@extends('layoutTemplate')
@section('title',$title)

@section('content')
<div class="card container-fluid">
  <div class="card-header bg-white row">
    <div class="col-md-6">
        <span class="h3">Mata Kuliah</span>
    </div>
    <div class="col-md-6">
        <form action="" method="get" class="row justify-content-end">
            <div class="form-group">
              <input type="text" class="form-control" name="num_form" placeholder="jumlah data">
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
            @isset($dosen_matkul)
                @if ($i<$dosen_matkul->count())
                    <input type="hidden" name="mata_kuliah[{{$i}}][id]" value="{{$dosen_matkul[$i]->id}}">
                @endif
            @endisset
            <div class="form-group col-md-10">
                <select class="form-control selectpicker" name="mata_kuliah[{{$i}}][kode_matkul]" data-live-search="true">
                    <option disabled
                    @if (!old('mata_kuliah.'.$i.'.kode_matkul'))
                    selected
                    @endif
                    > -- pilih mata kuliah -- </option>
                    @foreach ($mata_kuliah as $item)
                    <option value="{{$item->kode_matkul}}"
                        @isset($dosen_matkul)
                            @if ($i<$dosen_matkul->count())
                                @if ($item->kode_matkul === old('mata_kuliah.'.$i.'.kode_matkul',$dosen_matkul[$i]->kode_matkul))
                                    selected
                                @endif
                            @endif
                        @endisset
                        @if ($item->kode_matkul === old('mata_kuliah.'.$i.'.kode_matkul'))
                            selected
                        @endif
                        >{{$item->nama_matkul}} - {{$item->kode_matkul}}</option>
                        @endforeach
                    </select>
                    <small class="text-danger">
                        @error('mata_kuliah.'.$i.'.kode_matkul')
                            {{$message}}
                        @enderror
                    </small>
                    </div>
                    <div class="col-md text-center">
                        @isset($dosen_matkul)
                            @if ($i<$dosen_matkul->count())
                                <a href="{{URL::to('/master/dosen/detail/'.$dosen->id.'/mata-kuliah/hapus/'.$dosen_matkul[$i]->id)}}">
                                    <button class="btn btn-danger" type="button"><i class="fas fa-trash"></i></button>
                                </a>
                            @endif
                        @endisset
                        @isset($dosen_matkul)
                            @if ($i>=$dosen_matkul->count())
                                <button class="btn btn-danger" type="button" onclick="deleteForm(this)"><i class="fas fa-trash"></i></button>
                            @endif
                        @else
                            <button class="btn btn-danger" type="button" onclick="deleteForm(this)"><i class="fas fa-trash"></i></button>
                        @endisset
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
