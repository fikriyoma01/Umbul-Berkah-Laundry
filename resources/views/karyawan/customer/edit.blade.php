@extends('layouts.backend')
@section('title','Edit Customer')
@section('header','Edit Data Customer')
@section('content')
<div class="col-lg-12">
    <div class="card card-outline-info">
        <div class="card-header">
            <h4 class="card-title">Form Edit Data Customer</h4>
        </div>
        <div class="card-body">
            @foreach($pelanggan as $p)
            <form action="{{url('customers-saveeditcust')}}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$p->id}}"> <br/>
                <div class="form-body">
                    <div class="row p-t-20">
                        <div class="col-md-4">
                            <div class="form-group has-success">
                                <label class="control-label">Nama</label>
                                <input type="text" class="form-control form-control-danger @error('name') is-invalid @enderror" name="name" value="{{$p->name}}" placeholder="Nama Customer" autocomplete="off">
                                @error('name')
                                  <span class="invalid-feedback text-danger" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-4">
                            <div class="form-group has-success">
                                <label class="control-label">Email</label>
                                <input type="email" class="form-control form-control-danger @error('email') is-invalid @enderror" name="email" value="{{$p->email}}" placeholder="Alamat Email" autocomplete="off">
                                @error('email')
                                  <span class="invalid-feedback text-danger" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group has-success">
                                <label class="control-label">No. WhatsApp</label>
                                <input type="number" class="form-control form-control-danger @error('no_telp') is-invalid @enderror" name="no_telp" placeholder="Nomor WhatsApp" value="{{$p->no_telp}}" autocomplete="off">
                                @error('no_telp')
                                  <span class="invalid-feedback text-danger" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-12">
                            <div class="form-group has-success">
                                <label class="control-label">Alamat</label>
                                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Alamat Customer"> {{$p->alamat}}</textarea>
                                @error('alamat')
                                  <span class="invalid-feedback text-danger" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--/row-->
                </div>
                <input type="hidden" name="auth" value="Admin">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mr-1 mb-1">Simpan</button>
                    <a href=" {{url('customers')}}"  class="btn btn-outline-warning mr-1 mb-1">Batal</a>
                </div>
            </form>
            @endforeach
        </div>
    </div>
</div>
@endsection