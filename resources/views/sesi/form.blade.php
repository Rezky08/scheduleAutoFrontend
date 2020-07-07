@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Sesi Mulai</label>
        <input type="time" class="form-control" id="datetimepicker5" name="sesi_mulai"
        placeholder="Masukan Nama Sesian"
        @isset($sesi)
        value="{{old('sesi_mulai',$sesi->sesi_mulai)}}"
        @else
        value="{{old('sesi_mulai')}}"
        @endisset
        >
        @if ($errors->has('sesi_mulai'))
            @error('sesi_mulai')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">catatan : AM pukul 00.00 - 11.59 dan PM 12.00 - 23.59 </small>

        @endif
    </div>

    <div class="form-group">
        <label for="">Sesi Mulai</label>
        <input type="time" class="form-control" id="datetimepicker5" name="sesi_selesai"
        placeholder="Masukan Nama Sesian"
        @isset($sesi)
        value="{{old('sesi_selesai',$sesi->sesi_selesai)}}"
        @else
        value="{{old('sesi_selesai')}}"
        @endisset
        >
        @if ($errors->has('sesi_selesai'))
            @error('sesi_selesai')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">catatan : AM pukul 00.00 - 11.59 dan PM 12.00 - 23.59</small>

        @endif
    </div>

    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
