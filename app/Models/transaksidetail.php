<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class transaksidetail extends Model
{
    use Notifiable;
    protected $fillable = [
        'id_transaksis','harga_id','kg','hari','harga','disc','harga_akhir'
    ];

    public function price()
    {
      return $this->belongsTo(harga::class,'harga_id','id');
    }

    public function transaksi()
    {
      return $this->belongsTo(transaksi::class,'id_transaksis','id');
    }


}
