<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getTransaksi($type, $kode_transaksi = null)
    {
        $transaksi = DB::table('master_barangs as mb')
            ->leftJoin('detail_transaksis as dt', 'mb.id', '=', 'dt.master_barang_id')
            ->leftJoin('transaksis as t', 'dt.transaksi_id', '=', 't.id');
        if ($type == 'OUT') {
            $transaksi = $transaksi->selectRaw('t.kode_transaksi, t.created_at,
            SUM(CASE WHEN t.type = "OUT" THEN dt.quantity*dt.harga_barang ELSE 0 END) as grand_total');
            $transaksi = $transaksi->where('t.type', 'OUT');
        }

        if ($type == 'IN') {
            $transaksi = $transaksi->selectRaw('t.kode_transaksi, t.created_at,
            SUM(CASE WHEN t.type = "IN" THEN dt.quantity ELSE 0 END) as total_qty');
            $transaksi = $transaksi->where('t.type', 'IN');
        }

        if ($kode_transaksi) {
            $transaksi = $transaksi->whereRaw("t.kode_transaksi = '{$kode_transaksi}'");
        }
        $transaksi = $transaksi->groupByRaw('t.kode_transaksi')->get();

        return $transaksi;
    }

    public static function generateKodeTransaksi($type)
    {
        $kode_transaksi = DB::table('transaksis')->where('type', $type)->max('kode_transaksi');
        $addZero = '';
        $kode_transaksi = str_replace("T/{$type}/", "", $kode_transaksi);
        $kode_transaksi = (int) $kode_transaksi + 1;
        $incrementKodeTransaksi = $kode_transaksi;

        if (strlen($kode_transaksi) == 1) {
            $addZero = "0000";
        } elseif (strlen($kode_transaksi) == 2) {
            $addZero = "000";
        } elseif (strlen($kode_transaksi) == 3) {
            $addZero = "00";
        } elseif (strlen($kode_transaksi) == 4) {
            $addZero = "0";
        }

        $newKodeTransaksi = "T/{$type}/" . $addZero . $incrementKodeTransaksi;
        return $newKodeTransaksi;
    }
}
