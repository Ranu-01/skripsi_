<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\MasterBarang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function indexPenjualan(Request $request)
    {
        if ($request->ajax()) {
            $result = Transaksi::getTransaksi("OUT");

            return  datatables()->of($result)->make(true);
        }

        return view('pages.penjualan.history');
    }

    public function indexKasir(Request $request)
    {
        if ($request->ajax()) {
            $result = MasterBarang::getBarang();

            return  datatables()->of($result)->make(true);
        }
        return view('pages.penjualan.kasir');
    }

    public function indexPersediaanMasuk(Request $request)
    {
        if ($request->ajax()) {
            $result = MasterBarang::getBarang();

            return  datatables()->of($result)->make(true);
        }
        return view('pages.persediaan-masuk.create');
    }

    public function storePersediaanMasuk(Request $request)
    {
        $list_permintaans = json_decode($request->list_permintaans);
        DB::beginTransaction();
        try {
            $transaksi = new Transaksi;
            $transaksi->kode_transaksi = Transaksi::generateKodeTransaksi("IN");
            $transaksi->type = "IN";
            $transaksi->created_by = Auth::user()->id;
            $transaksi->save();
            foreach ($list_permintaans as $item) {
                $barang = MasterBarang::where('kode_barang', $item->kode_barang)->first();
                $detail = new DetailTransaksi;
                $detail->transaksi_id = $transaksi->id;
                $detail->master_barang_id = $barang->id;
                $detail->quantity = $item->jumlah;
                $detail->harga_barang = $barang->harga_barang;
                $detail->save();
            }
            DB::commit();
            return response()->json([], 200);
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
            return response()->json([], 400);
        }
    }

    public function storeKasir(Request $request)
    {
        $keranjangs = json_decode($request->keranjang);
        DB::beginTransaction();
        try {
            $transaksi = new Transaksi;
            $transaksi->kode_transaksi = Transaksi::generateKodeTransaksi("OUT");
            $transaksi->type = "OUT";
            $transaksi->created_by = Auth::user()->id;
            $transaksi->created_at = $request->tgl_penjualan;
            $transaksi->save();
            foreach ($keranjangs as $item) {
                $barang = MasterBarang::where('kode_barang', $item->kode_barang)->first();
                $detail = new DetailTransaksi;
                $detail->transaksi_id = $transaksi->id;
                $detail->master_barang_id = $barang->id;
                $detail->quantity = $item->jumlah;
                $detail->harga_barang = $barang->harga_barang;
                $detail->save();
            }
            DB::commit();
            return response()->json([], 200);
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
            return response()->json([], 400);
        }
    }

    public function getDetailPenjualan(Request $request)
    {
        if ($request->ajax()) {
            $result = DetailTransaksi::getDetails("OUT", $request->kode_transaksi);

            return  datatables()->of($result)->make(true);
        }
    }

    public function indexHistoryPersediaan(Request $request)
    {
        if ($request->ajax()) {
            $result = Transaksi::getTransaksi("IN");

            return  datatables()->of($result)->make(true);
        }
        return view('pages.persediaan-masuk.history');
    }

    public function getDetailPersediaan(Request $request)
    {
        if ($request->ajax()) {
            $result = DetailTransaksi::getDetails("IN", $request->kode_transaksi);

            return  datatables()->of($result)->make(true);
        }
    }
}
