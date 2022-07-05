<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{transaksi,User,harga,DataBank,transaksidetail};
use App\Http\Requests\{AddCustomerRequest,AddOrderRequest};
use App\Notifications\{OrderMasuk,OrderSelesai};
use App\Jobs\{OrderCustomerJob,DoneCustomerJob};
use Auth;
use PDF;
use Mail;
use carbon\carbon;
use Alert;
use Session;
use DB;

class PelayananController extends Controller

{

    // Halaman list order masuk
    public function index()
    {
      $order = transaksi::with('price')->where('user_id',Auth::user()->id)
      ->orderBy('id','DESC')->get();
      return view('karyawan.transaksi.order', compact('order'));
    }

    // Proses simpan order
    public function store(AddOrderRequest $request)
    {
//        $data = $request->all();
//        dd($data);
      try {
        $kgs          = $request->kg;
        $harga_ids    = $request->harga_id;
        $haris        = $request->hari;
        $hargas       = $request->harga;
        $discs        = $request->disc;                  
        DB::beginTransaction();
        $order = new transaksi();
        $order->invoice         = $request->invoice;
        $order->tgl_transaksi   = Carbon::now()->parse($order->tgl_transaksi)->format('d-m-Y');
        $order->status_payment  = $request->status_payment;
        $order->customer_id     = $request->customer_id;
        $order->user_id         = Auth::user()->id;
        $order->customer        = namaCustomer($order->customer_id);
        $order->email_customer  = email_customer($order->customer_id);        
        $order->harga_id        = $request->harga_id[0];
        $order->hari            = $request->totalHari;
        $order->kg              = $request->totalKG;
        $order->harga           = $request->totalHarga;
        $order->disc            = $request->disc[0];
        $hitung                 = $request->totalAll;
        $order->harga_akhir    = $hitung;
        $order->jenis_pembayaran  = $request->jenis_pembayaran;
        $order->tgl               = Carbon::now()->day;
        $order->bulan             = Carbon::now()->month;
        $order->tahun             = Carbon::now()->year;
        $order->save();

        if ($order) {
//          // Notification Telegram
//          if (setNotificationTelegramIn(1) == 1) {
//            $order->notify(new OrderMasuk());
//          }
//
//          // Notification email
//          if (setNotificationEmail(1) == 1) {
//            // Menyiapkan data Email
//            $bank = DataBank::get();
//            $jenisPakaian = harga::where('id', $order->harga_id)->first();
//            $data = array(
//                'email'         => $order->email_customer,
//                'invoice'       => $order->invoice,
//                'customer'      => $order->customer,
//                'tgl_transaksi' => $order->tgl_transaksi,
//                'pakaian'       => $jenisPakaian->jenis,
//                'berat'         => $order->kg,
//                'harga'         => $order->harga,
//                'harga_disc'    => ($hitung * $order->disc) / 100,
//                'disc'          => $order->disc,
//                'total'         => $order->kg * $order->harga,
//                'harga_akhir'   => $order->harga_akhir,
//                'laundry_name'  => Auth::user()->nama_cabang,
//                'bank'          => $bank
//            );
//
//            // Kirim Email
//            dispatch(new OrderCustomerJob($data));
//          }
            DB::commit();
            DB::beginTransaction();          
            foreach($kgs as $key => $kg) {
                $orderDtl               = new transaksidetail();
                $orderDtl->id_transaksis = $order->id;
                $orderDtl->harga_id      = isset($harga_ids[$key]) ? $harga_ids[$key] : 0;
                $orderDtl->kg            = $kg;
                $orderDtl->hari          = isset($haris[$key]) ? $haris[$key] : '0';
                $orderDtl->harga         = isset($hargas[$key]) ? $hargas[$key] : '0';
                $orderDtl->disc          = isset($discs[$key]) ? $discs[$key] : '0';
                $hitung                  = $orderDtl->kg * $orderDtl->harga;
                if ($orderDtl->disc != NULL) {
                    $disc                = ($hitung * $orderDtl->disc) / 100;
                    $total               = $hitung - $disc;
                    $orderDtl->harga_akhir  = $total;
                } else {
                    $orderDtl->harga_akhir    = $hitung;
                }                
                $orderDtl->save();                 
            }          
          DB::commit();
          Session::flash('success','Order Berhasil Ditambah !');
          return redirect('pelayanan');
        }
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }
    }

    // Tambah Order
    public function addorders()
    {
      $customer = User::where('karyawan_id',Auth::user()->id)->get();
      $jenisPakaian = harga::where('user_id',Auth::id())->where('status','1')->get();
    //   $list_harga = harga::select('id','harga')
    //   ->where('user_id',Auth::user()->id)
    //   ->where('id',$request->id)
    //   ->get();

      $y = date('Y');
      $number = mt_rand(1000, 9999);
      // Nomor Form otomatis
      $newID = $number. Auth::user()->id .''.$y;
      $tgl = date('d-m-Y');

      $cek_harga = harga::where('user_id',Auth::user()->id)->where('status',1)->first();
      $cek_customer = User::select('id','karyawan_id')->where('karyawan_id',Auth::id())->count();
      return view('karyawan.transaksi.addorder', compact('customer','newID','cek_harga','cek_customer','jenisPakaian'));
    }

    // Filter List Harga
    public function listharga(Request $request)
    {
       $list_harga = harga::select('id','harga')
        ->where('user_id',Auth::user()->id)
        ->where('id',$request->id)
        ->get();
        $select = '';
        $select .= '
                    <div class="form-group has-success">
                    <label for="id" class="control-label">Harga</label>
                    <select id="harga" class="form-control" name="harga" value="harga">
                    ';
                    foreach ($list_harga as $studi) {
        $select .= '<option value="'.$studi->harga.'">'.'Rp. ' .number_format($studi->harga,0,",",".").'</option>';
                    }'
                    </select>
                    </div>
                    </div>';
        return $select;
    }

    // Filter List Jumlah Hari
    public function listhari(Request $request)
    {
      $list_jenis = harga::select('id','hari')
        ->where('user_id',Auth::user()->id)
        ->where('id',$request->id)
        ->get();
        $select = '';
        $select .= '
                    <div class="form-group has-success">
                    <label for="id" class="control-label">Pilih Hari</label>
                    <select id="hari" class="form-control" name="hari" value="hari">
                    ';
                    foreach ($list_jenis as $hari) {
        $select .= '<option value="'.$hari->hari.'">'.$hari->hari.'</option>';
                    }'
                    </select>
                    </div>
                    </div>';
        return $select;
    }


    // Update Status Laundry
    public function updateStatusLaundry(Request $request)
    {
      $transaksi = transaksi::find($request->id);
      if ($transaksi->status_payment == 'Pending') {
        $transaksi->update([
          'status_payment' => 'Success'
        ]);
      } elseif ($transaksi->status_payment == 'Success') {
        if ($transaksi->status_order == 'Process') {
          $transaksi->update([
            'status_order' => 'Done'
          ]);

            // Tambah point +1
            $points = User::where('id',$transaksi->customer_id)->firstOrFail();
            $points->point =  $points->point + 1;
            $points->update();

            // Cek email notif
            if (setNotificationEmail(1) == 1) {

              // Menyiapkan data
              $data = array(
                  'email'           => $transaksi->email_customer,
                  'invoice'         => $transaksi->invoice,
                  'customer'        => $transaksi->customer,
                  'nama_laundry'    => Auth::user()->nama_cabang,
                  'alamat_laundry'  => Auth::user()->alamat_cabang,
              );

            // Kirim Email
            dispatch(new DoneCustomerJob($data));
            }

            // Cek status notif untuk telegram
            if (setNotificationTelegramFinish(1) == 1) {
              $transaksi->notify(new OrderSelesai());
            }

            // Notifikasi WhatsApp
            if (setNotificationWhatsappOrderSelesai(1) == 1 && getTokenWhatsapp() != null) {
              $waCustomer = $transaksi->customers->no_telp; // get nomor whatsapp customer
              $nameCustomer = $transaksi->customers->name; // get name customer
              notificationWhatsapp(
                getTokenWhatsapp(), // Token
                $waCustomer, // nomor whatsapp
                'Halo Kak '.$nameCustomer.' Laundry kamu sudah selesai dan sudah bisa diambil nih :) ' // pesan
              );
            }

        } elseif ($transaksi->status_order == 'Done') {
          $transaksi->update([
            'status_order' => 'Delivery'
          ]);
        }
      }

      if ($transaksi->status_payment == 'Success') {
          Session::flash('success', "Status Pembayaran Berhasil Diubah !");
      }
      if($transaksi->status_order == 'Done' || $transaksi->status_order == 'Delivery') {
          Session::flash('success', "Status Laundry Berhasil Diubah !");
      }
    }

    // Filter List Harga
    public function listhargatransaksi(Request $request)
    {
       $list_harga = harga::select('id','harga')
        ->where('user_id',Auth::user()->id)
        ->where('id',$request->id)
        ->get();
        $select = '';
        $select .= '
                    <div class="form-group has-success">
                    <label for="id" class="control-label">Harga</label>
                    <select id="harga" class="form-control" name="harga[]" value="harga">
                    ';
                    foreach ($list_harga as $studi) {
        $select .= '<option value="'.$studi->harga.'">'.'Rp. ' .number_format($studi->harga,0,",",".").'</option>';
                    }'
                    </select>
                    </div>
                    </div>';
        return $select;
    }

    // Filter List Jumlah Hari
    public function listharitransaksi(Request $request)
    {
      $list_jenis = harga::select('id','hari')
        ->where('user_id',Auth::user()->id)
        ->where('id',$request->id)
        ->get();
        $select = '';
        $select .= '
                    <div class="form-group has-success">
                    <label for="id" class="control-label">Pilih Hari</label>
                    <select id="hari" class="form-control" name="hari[]" value="hari">
                    ';
                    foreach ($list_jenis as $hari) {
        $select .= '<option value="'.$hari->hari.'">'.$hari->hari.'</option>';
                    }'
                    </select>
                    </div>
                    </div>';
        return $select;
    }

}
