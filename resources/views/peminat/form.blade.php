@extends('layoutTemplate')
@section('title',$title)

@section('content')

<form action="{{$action}}" method="post">
    @csrf
    <div class="form-group">
        <label for="">Tahun Ajaran</label>
        <input type="text" class="form-control" name="tahun_ajaran"
        placeholder="Masukan Tahun Ajaran"
        @isset($peminat)
        value="{{old('tahun_ajaran',$peminat->tahun_ajaran)}}"
        @else
        value="{{old('tahun_ajaran')}}"
        @endisset
        >
        @if ($errors->has('tahun_ajaran'))
            @error('tahun_ajaran')
                <small class="form-text text-danger">{{$message}}</small>
            @enderror
            @else
                <small class="form-text text-muted">Tahun ajaran harus dipisahkan dengan "/" eg: 2019/2020</small>

        @endif
    </div>

    <div class="form-group">
      <label for="">Semester</label>
      <select class="form-control" name="semester">
          <option value="" disabled
          @isset($peminat)
            @else
            selected
          @endisset
          @if (!old('semester'))
              selected
          @endif
          >-- Pilih Semester --</option>
        @foreach ($semester as $item)
            <option value="{{$item->semester}}"
                @isset($peminat)
                    @if ($peminat->semester == $item->semester)
                        selected
                    @endif
                @endisset
                @if (old('semester')==$item->semester)
                    selected
                @endif
                >{{$item->keterangan}}</option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-primary float-right mb-3">Simpan</button>
</form>


@endsection
