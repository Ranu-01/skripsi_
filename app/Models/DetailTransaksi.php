<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DetailTransaksi extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function barangs(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class);
    }

    public static function getDetails($type, $kode_transaksi)
    {
        $transaksi = DB::table('master_barangs as mb')
            ->leftJoin('detail_transaksis as dt', 'mb.id', '=', 'dt.master_barang_id')
            ->leftJoin('transaksis as t', 'dt.transaksi_id', '=', 't.id');
        if ($type == 'OUT') {
            $transaksi = $transaksi->selectRaw('mb.kode_barang, mb.nama_barang, dt.harga_barang, dt.quantity,
            (CASE WHEN t.type = "OUT" THEN dt.quantity*dt.harga_barang ELSE 0 END) as sub_total');
            $transaksi = $transaksi->where('t.type', 'OUT');
        }

        if ($type == 'IN') {
            $transaksi = $transaksi->selectRaw('mb.kode_barang, mb.nama_barang, dt.harga_barang,
            (CASE WHEN t.type = "IN" THEN dt.quantity ELSE 0 END) as qty');
            $transaksi = $transaksi->where('t.type', 'IN');
        }

        if ($kode_transaksi) {
            $transaksi = $transaksi->whereRaw("t.kode_transaksi = '{$kode_transaksi}'");
        }
        $transaksi = $transaksi->get();

        return $transaksi;
    }
}
