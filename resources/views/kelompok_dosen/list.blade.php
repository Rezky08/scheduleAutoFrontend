@extends('layoutTemplate')
@section('title',$title)


@section('content')
<div class="row">
    <form action="" method="get" class="col-md-8 row">
        <div class="col-md-5 px-0">
            <div class="form-group">
                <input type="text"
                class="form-control" name="searchbox" placeholder="masukan tahun ajaran ...">
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>
    <div class="col-md-4 text-right px-0">
        <a href="{{URL::to('/penjadwalan/kelompok-dosen/tambah')}}">
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
                <th>Tahun Ajaran</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Score</th>
                <th>Last Update</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelompok_dosen as $item)
            <tr align="center">
                <td>{{$item->id}}</td>
                <td>{{$item->peminat->tahun_ajaran}}</td>
                <td>{{$item->peminat->semester_detail->keterangan}}</td>
                <td>{{$item->process_log->status}}</td>
                <td>{{$item->process_log->fitness}}</td>
                <td>{{$item->process_log->updated_at}}</td>
                <td width="10%">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                            Pilih Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{URL::to('/penjadwalan/kelompok-dosen/detail/'.$item->id)}}">
                                <span>
                                    <i class="fas fa-info"></i>
                                    Detail
                                </span>
                            </a>
                            <a class="dropdown-item" href="{{URL::to('/penjadwalan/kelompok-dosen/ubah/'.$item->id)}}">
                                <span>
                                    <i class="fas fa-edit"></i>
                                    Ubah
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" data-toggle="modal" data-target="#modelId" onclick="deleteModal({{$item->id}})">
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
        {{$kelompok_dosen->links()}}
    </div>
</div>
@endsection

<script>
    function deleteModal(id) {
        let selected_element = kelompok_dosen.data.find(function (data) {
            return data.id==id
         });
        let textString = "Apakah anda ingin menghapus Kelompok Dosen "+selected_element.id+" ?";
        $("#modelId .modal-body").text(textString);
        let url = "{{URL::to('/penjadwalan/kelompok-dosen/hapus/')}}"+"/"+selected_element.id;
        $("#modelId .modal-footer a").attr('href',url);
    }
</script>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Ruang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus Kelompok Dosen
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                <a href="#"><button type="button" class="btn btn-danger">Hapus</button></a>
            </div>
        </div>
    </div>
</div>
