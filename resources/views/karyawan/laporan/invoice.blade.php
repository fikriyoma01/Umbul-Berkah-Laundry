@extends('layouts.backend')
@section('title','Karyawan - Invoice Customer')
@section('header','Invoice Customer')
@section('content')
<div class="col-md-12">
    <div class="card card-body printableArea">
        <h3><b>INVOICE</b> <span class="pull-right">{{$data->invoice}}</span></h3>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <address>
                        <h3> &nbsp;<b class="text-danger">{{$data->user->nama_cabang}}</b></h3>
                        <p class="text-muted m-l-5"> Diterima Oleh <span style="margin-left:20px"> </span>: {{$data->user->name}}
                            <br/> Alamat <span style="margin-left:70px"> </span>: {{$data->user->alamat_cabang}},
                            <br/> No. Telp <span style="margin-left:65px"> </span>: {{$data->user->no_telp}}
                        </p>
                    </address>
                </div>
                <div class="pull-right text-right">
                    <address>
                        <h3>Detail Order Customer :</h3>
                        <p class="text-muted m-l-30">
                            {{$data->customers->nama}}
                            <br/> {{$data->customers->alamat}}
                            <br/> {{$data->customers->no_telp}}</p>
                        <p class="m-t-30"><b>Tanggal Masuk :</b> <i class="fa fa-calendar"></i> {{carbon\carbon::parse($data->tgl_transaksi)->format('d-m-Y')}}</p>
                        <p><b>Tanggal Diambil :</b> <i class="fa fa-calendar"></i>
                            @if ($data->tgl_ambil == "")
                            Belum Diambil
                            @else
                            {{\carbon\carbon::parse($data->tgl_ambil)->format('d-m-Y')}}
                            @endif
                        </p>
                    </address>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive m-t-20" style="clear: both;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Jenis Pakaian</th>
                                <th class="text-right">Berat</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Diskon</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoiceDtl as $key => $item)
                            <tr>
                                <td class="text-center">{{$key+1}}</td>
                                <td>{{$item->price->jenis}}</td>
                                <td class="text-right">{{$item->kg}} Kg</td>
                                <td class="text-right">{{Rupiah::getRupiah($item->harga)}} /kg</td>
                                <td class="text-right">{{Rupiah::getRupiah((($item->harga*$item->kg)*$item->disc)/100)}}</td>
                                <td class="text-right">                                        
                                        <p>{{Rupiah::getRupiah($item->harga_akhir)}}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="pull-left m-t-10">
                    <h6 style="font-weight:bold">Metode Pembayaran :</h6>
                    <ol>
                        @foreach ($bank as $banks)
                        <li style="color: white"> {{$banks->nama_bank}} <br> {{$banks->no_rekening}} a/n {{$banks->nama_pemilik}}</li>
                        @endforeach
                    </ol>
                </div>
                @foreach ($invoice as $itemHdr)
                <div class="pull-right m-t-10 text-right">
                    <p>Total : {{Rupiah::getRupiah($itemHdr->harga)}}</p>
                    <p>Disc  : {{Rupiah::getRupiah($itemHdr->harga - $itemHdr->harga_akhir)}} </p>
                    <hr>
                    <h3><b>Total Bayar :</b> {{Rupiah::getRupiah($itemHdr->harga_akhir)}}</h3>
                </div>
                @endforeach
                <div class="clearfix"></div>
                <hr>
                <div class="text-right">
                    <a href="{{url('pelayanan')}}" class="btn btn-outline btn-danger" style="color:white">Back</a>
                    <a href="{{url('cetak-invoice/'.$itemHdr->id. '/print')}}" target="_blank" class="btn btn-success"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection