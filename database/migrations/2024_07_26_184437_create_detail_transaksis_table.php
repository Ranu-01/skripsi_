<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id()->start_from(0);
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('master_barang_id');
            $table->integer('quantity');
            $table->integer('harga_barang');
            $table->timestamps();

            $table->foreign('transaksi_id')
                ->references('id')->on('transaksis')
                ->onDelete('cascade');

            $table->foreign('master_barang_id')
                ->references('id')->on('master_barangs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
