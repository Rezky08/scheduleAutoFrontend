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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPelanggaran">
            Pelanggaran
        </button>

        <a href="{{URL::to('/penjadwalan/kelompok-dosen/detail/'.$kelompok_dosen->id.'/tambah')}}">
            <button class="btn btn-info">
                Tambah +
            </button>
        </a>
    </div>
</div>

<div class="row card">
    <div class="card-header">
        Total data : {{$kelompok_dosen_detail->total()}}
    </div>
    <table class="table table-hover">
        <thead>
            <tr align="center">
                <th>Id</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Kelompok</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelompok_dosen_detail as $item)
            <tr align="center">
                <td>{{$item->id}}</td>
                <td>{{$item->mata_kuliah->nama_matkul}}</td>
                <td>{{$item->dosen->nama_dosen}}</td>
                <td>{{$item->kelompok}}</td>
                <td>{{$item->kapasitas}}</td>
                <td width="10%">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                            Pilih Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{URL::to('/penjadwalan/kelompok-dosen/detail/'.$kelompok_dosen->id.'/ubah/'.$item->id)}}">
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
        {{$kelompok_dosen_detail->links()}}
    </div>
</div>
@endsection

<script>
    function deleteModal(id) {
        let selected_element = kelompok_dosen_detail.data.find(function (data) {
            return data.id==id
         });
        let textString = "Apakah anda ingin menghapus Id "+selected_element.id+" ?";
        $("#modelId .modal-body").text(textString);
        let url = "{{URL::to('/penjadwalan/kelompok-dosen/detail/'.$kelompok_dosen->id.'/hapus/')}}"+"/"+selected_element.id;
        $("#modelId .modal-footer a").attr('href',url);
    }
</script>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Kelompok Dosen Detail</h5>
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

<!-- Modal -->
<div class="modal fade" id="modalPelanggaran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pelanggaran Terhadap Aturan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <form action="{{$action}}" method="post">
                @csrf
                <div class="modal-body">
                    @empty($pelanggaran)
                    <span><i>Tidak ada pelanggaran</i></span>
                    @else

                    @php
                        $index = 0 ;
                    @endphp
                    @forelse ($pelanggaran as $item)
                    <div class="card my-2">
                        <div class="card-header">{{$item->dosen->nama_dosen}} ({{$item->kode_dosen}})</div>
                        <div class="card-body">
                            @foreach ($kelompok_dosen_detail_all->where('kode_dosen',$item->kode_dosen) as $item_pelanggaran)
                            <div class="row">
                                <input type="hidden" name="kelompok_dosen_detail[{{$index}}][id]" value="{{$item_pelanggaran->id}}">
                                <div class="col-md-6">
                                    <span>{{$item_pelanggaran->mata_kuliah->nama_matkul}} ({{$item_pelanggaran->kode_matkul}}) - {{$item_pelanggaran->kelompok}}</span>
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="form-control selectpicker" name="kelompok_dosen_detail[{{$index}}][kode_dosen]" data-live-search="true">
                                        <option disabled
                                        @if (!old('kelompok_dosen_detail.'.$index.'.kode_dosen'))
                                        selected
                                        @endif

                                        > -- pilih Dosen -- </option>
                                        @foreach ($item_pelanggaran->mata_kuliah->dosen_matkul as $item_dosen)
                                        <option value="{{$item_dosen->kode_dosen}}"
                                            @if ($item_dosen->kode_dosen == old('kelompok_dosen_detail.'.$index.'.kode_dosen',$item->kode_dosen))
                                                selected
                                            @endif
                                            >{{$item_dosen->dosen->nama_dosen}} - {{$item_dosen->kode_dosen}} ( {{$pelanggaran->where('kode_dosen',$item_dosen->kode_dosen)->first()?$pelanggaran->where('kode_dosen',$item_dosen->kode_dosen)->first()->count:0}} Kelompok)</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger">
                                            @error('kelompok_dosen_detail.'.$index.'.kode_dosen')
                                                {{$message}}
                                            @enderror
                                        </small>
                                </div>
                            </div>
                            @php
                                $index++;
                            @endphp
                            @endforeach
                        </div>
                    </div>

                    @empty
                        <span class="h3"><i>Tidak ada pelanggaran</i></span>
                    @endforelse

                    @endempty
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    @empty($pelanggaran)
                    @else
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endempty
                </div>
            </form>
        </div>
    </div>
</div>
