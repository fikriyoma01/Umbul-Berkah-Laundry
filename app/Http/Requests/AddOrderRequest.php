<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array();
//        foreach ($this->input('gr') as $index => $grs) {
//            $gr = 'gr.' . $index ;
//            $rules[$gr] = 'required|regex:/^[0-9.]+$/|numeric';
//        }        
        return array_merge([
          'status_payment'    => 'required',
//          'kg'                => 'required|regex:/^[0-9.]+$/|numeric',
//          'hari'              => 'required',
//          'harga'             => 'required',
          'jenis_pembayaran'  => 'required',
//          'disc'              => 'nullable|numeric',
//          'harga_id'          => 'required',
          'customer_id'       => 'required'
        ],$rules);
            
    }

    public function messages()
    {
      $array = array (
        'status_payment.required'   => 'Status Pembayaran wajib dipilih.',
//        'kg.required'               => 'Berat Pakaian tidak boleh kosong.',
//        'kg.numeric'                => 'Berat Pakaian hanya mendukung angka.',
//        'hari.required'             => 'Hari tidak boleh kosong.',
//        'harga.required'            => 'Harga tidak boleh kosong.',
        'jenis_pembayaran.required' => 'Jenis Pembayaran wajib dipilih.',
//        'disc.numeric'              => 'Diskon hanya mendukung angka.',
//        'harga_id.required'         => 'Jenis Pakaian wajib dipilih.',
        'customer_id.required'      => 'Customer wajib dipilih.'
        );
//        foreach ($this->input('gr') as $index => $grs) {
//            $gr = 'gr.' . $index ;
//            $array[$gr.'.required'] = 'Berat gr tidak boleh kosong.';
//            $array[$gr.'.numeric'] = 'Berat gr hanya mendukung angka.';
//        }              
        return $array;                  
    }
}

//---
//Insert Multiple Data Pada Laravel part 1
//Insert Multiple Data Pada Laravel part 2
//Insert Multiple Data Pada Laravel part 3
//---
//<form ...
//{{ csrf_field() }}
//name = xxx[]
//
//@csrf
//
//
//
//---------
//
//
//$data = $request->all();
//dd($data);
//
//$customer = new Customer;
//$customer->nama = $data['nama'];
//$customer->email = $data['email'];
//$customer->save();
//
//$detail = new Detail;
//$detail->user_id = $customer->id;
//$detail->address = $data['address];
//$detail->phone = $data['phone];
//$detail->zip = $data['zip];
//$detail->save();
//
//if(count($data['address'])>0){
//    foreach($data['address'] as $item => $value){
//        $data2 = array(
//            'user_id' => $customer->id,
//            'address' => $data['address'][$item],
//            'phone'   => $data['phone'][$item],
//            'zip'     => $data['zip'][$item],                    
//        );
//        Detail::create($data2);
//    }
//}
//---