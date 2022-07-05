<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksidetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksidetails', function (Blueprint $table) {

            $table->unsignedBigInteger('id_transaksis');
            $table->id();
            $table->integer('harga_id');
            $table->string('kg');
            $table->string('hari');
            $table->string('harga');
            $table->string('disc')->nullable();
            $table->string('harga_akhir')->nullable();
            $table->timestamps();

            $table->foreign('id_transaksis')->references('id')->on('transaksis')->onDelete('cascade')->onUpdate('cascade');
            //$table->foreign('harga_id')->references('id')->on('hargas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksidetails');
    }
}
