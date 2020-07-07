@extends('layoutTemplate')
@section('title',$title)

@section('content')
<div class="row card">
    <form action="{{$action}}" method="post">
        @csrf
        <table class="table table-hover">
            <thead>
                <tr align="center">
                    <th>No</th>
                    <th>Kode Mata Kuliah</th>
                    <th>Jumlah Peminat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminat_detail as $index=>$item)
                <tr>
                    <td align="center">{{$index+1}}</td>
                    <td>
                        <input type="text" name="{{"peminat_detail[".$index."][kode_matkul]"}}" class="form-control" value="{{$item['kode_matkul']}}">
                        @if ($errors->has("peminat_detail.".$index.".kode_matkul"))
                            @error("peminat_detail.".$index.".kode_matkul")
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        @endif
                    </td>
                    <td>
                        <input type="text" name="{{"peminat_detail[".$index."][jumlah_peminat]"}}" class="form-control" value="{{$item['jumlah_peminat']}}">
                        @if ($errors->has("peminat_detail.".$index.".jumlah_peminat"))
                            @error("peminat_detail.".$index.".jumlah_peminat")
                                <small class="text-danger">{{$message}}</small>
                            @enderror
                        @endif
                    </td>
                    <td width="10%">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                Pilih Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-toggle="modal" data-target="#modelId" onclick="deleteModal({{$index}},this)">
                                    <span>
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </span>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="text-center"><strong>Data Tidak Ada</strong></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary float-right mx-2">Simpan</button>
            <a href="{{URL::to('/master/peminat/detail/'.$peminat->id)}}"><button type="button" class="btn btn-danger float-right">Batal</button></a>
        </div>
    </form>
</div>
@endsection

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Peminat Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus Peminat Mata Kuliah
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="deleteItem()">Hapus</button>
            </div>
        </div>
    </div>
</div>

@section('script')

<script>
    function deleteModal(index,selector) {
        let selected_element = peminat_detail[index];
        let textString = "Apakah anda ingin menghapus Peminat Mata Kuliah "+selected_element.kode_matkul+" ?";
        let parent = $(selector).closest('tr');
        $(parent).attr('id', 'deletedSoon');
        $("#modelId .modal-body").text(textString);
    }
    function deleteItem() {
        $("#deletedSoon").remove();
     }
</script>
@endsection
