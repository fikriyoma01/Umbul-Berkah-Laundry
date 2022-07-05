<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use ErrorException;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\AddCustomerRequest;
use Illuminate\Support\Facades\Hash;
use App\Jobs\RegisterCustomerJob;
use Mail;
use DB;
class CustomerController extends Controller
{
    // index
    public function index()
    {
      $customer = User::where('karyawan_id',Auth::user()->id)
      ->where('auth','Customer')
      ->orderBy('id','DESC')->get();
      return view('karyawan.customer.index', compact('customer'));
    }

    // Detail Customer
    public function detail($id)
    {
      $customer = User::with('transaksiCustomer')
      ->where('karyawan_id',Auth::user()->id)
      ->where('id',$id)->first();
      return view('karyawan.customer.detail', compact('customer'));
    }

    // Create
    public function create()
    {
      return view('karyawan.customer.create');
    }

    // Store
    public function store(AddCustomerRequest $request)
    {

      try {
        DB::beginTransaction();
        $cekNumber = substr($request->no_telp,0,1); // ambil angka pertama dari string
        $cekNumber1 = substr($request->no_telp,0,2); // ambil angka pertama & kedua dari string

        if ($cekNumber == 0) { // cek jika angka pertama sama dengan 0, jalankan perintah ini
          $removeNol = '62'. ltrim($request->no_telp, 0); // Hapus angka kosong
        } elseif($cekNumber1 == 62) { // cek jika angka pertama & kedua sama dengan 62, jalankan perintah ini
          $removeNol = $request->no_telp; // Balikan jika format sudah benar
        }

        $password =$this->acakpass(8);

        $addCustomer = User::create([
          'karyawan_id' => Auth::id(),
          'name'        => $request->name,
          'email'       => $request->email,
          'auth'        => 'Customer',
          'status'      => 'Active',
          'no_telp'     => $removeNol,
          'alamat'      => $request->alamat,
          'password'    => Hash::make($password)
        ]);

        $addCustomer->assignRole($addCustomer->auth);

//        if ($addCustomer) {
//          // Menyiapkan data Email
//          $data = array(
//              'name'            => $addCustomer->name,
//              'email'           => $addCustomer->email,
//              'password'        => $password,
//              'url_login'       => url('/login'),
//              'nama_laundry'    => Auth::user()->nama_cabang,
//              'alamat_laundry'  => Auth::user()->alamat_cabang,
//          );
//          // Kirim email
//          dispatch(new RegisterCustomerJob($data));
//        }
        DB::commit();
        Session::flash('success','Customer Berhasil Ditambah ! /nPassword Customer adalah = '.$password);
        return redirect('customers');
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }
    }

    // Acak Password
    private function acakpass($long){
      $huruf = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'; //buat karakter yang akan digunakan sebagai password
      $st = '';
      for($i=0; $i<$long; $i++){
        $p = rand(0, strlen($huruf)-1);
        $st .=$huruf[$p];
      }
      return $st;
    }

    // Hapus
    public function deletecust($id)
    {
      try {
        DB::beginTransaction();
	      // menghapus data pelanggan berdasarkan id yang dipilih
	      DB::table('users')->where('id',$id)->delete();
        DB::commit();
        Session::flash('success','Data pelanggan sudah sukses dihapus');
        return redirect('customers');
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }      
    }  

    //Edit pelanggan
    public function editcust($id)
    {
      $pelanggan = DB::table('users')->where('id',$id)->get();
      return view('karyawan.customer.edit',['pelanggan' => $pelanggan]);
    }    

    //save edit pelanggan    
    public function saveeditcust(Request $request){
      try {
        DB::beginTransaction();      
        // update data pelanggan
        DB::table('users')->where('id',$request->id)->update([
          'name' => $request->name,
          'email' => $request->email,
          'no_telp' => $request->no_telp,
          'alamat' => $request->alamat
        ]);
        DB::commit();
        Session::flash('success','Perubahan data pelanggan sukses');
        return redirect('customers');      
      } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
      }         
    }
   
}
