@extends('layoutTemplate')
@section('title',$title)


@section('content')
<div class="row">
    <form action="" method="get" class="col-md-8 row">
        <div class="col-md-5 px-0">
            <div class="form-group">
                <input type="text"
                class="form-control" name="searchbox" placeholder="masukan nama mata kuliah ...">
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>
    <div class="col-md-4 text-right px-0">
        <div class="dropdown">
            <button class="btn btn-info" data-toggle="dropdown">
                Tambah +
            </button>
            <div class="dropdown-menu" aria-labelledby="triggerId">
                <a class="dropdown-item" href="{{URL::to('/master/peminat/detail/'.$peminat->id.'/tambah')}}">
                    Tambah Data
                </a>
                <button class="dropdown-item" data-toggle="modal" data-target="#modalFormBatch">Tambah File</button>
            </div>
        </div>
    </div>
</div>

<div class="row card">
    <div class="card-header">
        <span>Total Data : {{$peminat_detail->total()}} Records</span>
    </div>
    <table class="table table-hover">
        <thead>
            <tr align="center">
                <th>Id</th>
                <th>Kode Mata Kuliah</th>
                <th>Nama Mata Kuliah</th>
                <th>Jumlah Peminat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminat_detail as $item)
            <tr align="center">
                <td>{{$item->id}}</td>
                <td>{{$item->kode_matkul}}</td>
                <td>{{$item->mata_kuliah->nama_matkul}}</td>
                <td>{{$item->jumlah_peminat}}</td>
                <td width="10%">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                            Pilih Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{URL::to('/master/peminat/detail/'.$peminat->id.'/ubah/'.$item->id)}}">
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
        {{$peminat_detail->links()}}
    </div>
</div>
@endsection

<!-- Modal -->
<div class="modal fade" id="modalFormBatch" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peminat Mata Kuliah Batch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <form action="{{$action}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center row justify-content-center">
                        <div class="col-md-6 text-center">
                            <img src="{{ asset('img/contoh-data.jpg') }}" class="img-fluid" alt="contoh data">
                        </div>
                        <small class="col-md-12 text-center"><span class="text-info">Contoh Data Excell</span></small>
                    </div>
                    <div class="form-group">
                        <label for="">Peminat Mata Kuliah</label>
                        <input type="file" class="form-control-file" name="file_peminat_detail">
                        @if ($errors->has('file_peminat_detail'))
                        @error('file_peminat_detail')
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                        @else
                        <small class="form-text text-muted">Hanya menerima file Excell</small>
                        @endif
                    </div>
                    <div class="form-group">
                      <label for="">Baris Data</label>
                      <input type="text" class="form-control" name="data_row" placeholder="Baris dimulai data">
                      @if ($errors->has('data_row'))
                        @error('data_row')
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                        @else
                        <small class="form-text text-muted">Contoh: 2</small>
                        @endif
                    </div>
                    <div class="form-group">
                      <label for="">Kolom Kode Mata Kuliah</label>
                      <input type="text" class="form-control" name="kode_matkul_column" placeholder="Koordinat Dimulai Data">
                      @if ($errors->has('kode_matkul_column'))
                        @error('kode_matkul_column')
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                        @else
                        <small class="form-text text-muted">Contoh: A</small>
                        @endif
                    </div>
                    <div class="form-group">
                      <label for="">Kolom Jumlah Peminat</label>
                      <input type="text" class="form-control" name="jumlah_peminat_column" placeholder="Koordinat Dimulai Data">
                      @if ($errors->has('jumlah_peminat_column'))
                        @error('jumlah_peminat_column')
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                        @else
                        <small class="form-text text-muted">Contoh: B</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary">Bersihkan</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                <a href="#"><button type="button" class="btn btn-danger">Hapus</button></a>
            </div>
        </div>
    </div>
</div>

@section('script')

<script>
    function deleteModal(id) {
        let selected_element = peminat_detail.find(function (data) {
            return data.id==id
         });
        let textString = "Apakah anda ingin menghapus Peminat Mata Kuliah "+selected_element.kode_matkul+" ?";
        $("#modelId .modal-body").text(textString);
        let url = "{{URL::to('/master/peminat/detail/'.$peminat->id.'/hapus/')}}"+"/"+selected_element.id;
        console.log(url);
        $("#modelId .modal-footer a").attr('href',url);
    }
</script>
@endsection
