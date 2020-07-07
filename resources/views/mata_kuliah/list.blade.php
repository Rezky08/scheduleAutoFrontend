@extends('layoutTemplate')
@section('title',$title)


@section('content')
<div class="row">
    <form action="" method="get" class="col-md-8 row">
        <div class="col-md-5 px-0">
            <div class="form-group">
                <input type="text"
                class="form-control" name="searchbox" placeholder="masukan nama Mata Kuliah ...">
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>
    <div class="col-md-4 px-0">
        <a href="{{URL::to('/master/mata-kuliah/tambah')}}" class="float-right">
            <button class="btn btn-info">
                Tambah +
            </button>
        </a>
    </div>
</div>

<div class="row card">
    <table class="table table-hover">
        <thead>
            <tr align="center">
                <th>Id</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>SKS</th>
                <th>Status</th>
                <th>Ruang</th>
                <th>Program Studi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mata_kuliah as $item)
            <tr align="center">
                <td>{{$item['id']}}</td>
                <td>{{$item['kode_matkul']}}</td>
                <td align="left">{{$item['nama_matkul']}}</td>
                <td>{{$item['sks_matkul']}}</td>
                <td>{{$item['status_matkul']?"Aktif":"Tidak Aktif"}}</td>
                <td>{{$item['lab_matkul']?"LAB":"KELAS"}}</td>
                <td>{{$item['kode_prodi']}}</td>
                <td width="10%">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                            Pilih Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{URL::to('/master/mata-kuliah/detail/'.$item['id'])}}">
                                <span>
                                    <i class="fas fa-info"></i>
                                    Detail
                                </span>
                            </a>
                            <a class="dropdown-item" href="{{URL::to('/master/mata-kuliah/ubah/'.$item['id'])}}">
                                <span>
                                    <i class="fas fa-edit"></i>
                                    Ubah
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" data-toggle="modal" data-target="#modelId" onclick="deleteModal({{$item['id']}})">
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
</div>
<div class="row justify-content-center">
    <div class="col-md-6">
        {{$mata_kuliah->links()}}
    </div>
</div>
@endsection

<script>
    function deleteModal(id) {
        let selected_element = mata_kuliah.data.find(function (data) {
            return data.id==id
         });
        let textString = "Apakah anda ingin menghapus Mata Kuliah "+ selected_element.nama_matkul+"("+selected_element.kode_matkul+") ?";
        $("#modelId .modal-body").text(textString);
        let url = "{{URL::to('/master/mata-kuliah/hapus/')}}"+"/"+selected_element.id;
        console.log(url);
        $("#modelId .modal-footer a").attr('href',url);
    }
</script>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Program Studi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus mata kuliah
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                <a href="#"><button type="button" class="btn btn-danger">Hapus</button></a>
            </div>
        </div>
    </div>
</div>
