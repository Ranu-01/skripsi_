<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use App\Models\MasterPegawai;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $result["total_jenis"] = MasterBarang::all()->count('id');
        // dd($total_jenis);
        $result["total_pendapatan"] = DB::table('master_barangs as mb')
            ->leftJoin('detail_transaksis as dt', 'mb.id', '=', 'dt.master_barang_id')
            ->leftJoin('transaksis as t', 'dt.transaksi_id', '=', 't.id');

        $result["total_pendapatan"] = $result["total_pendapatan"]->selectRaw('
            SUM(CASE WHEN t.type = "OUT" THEN dt.quantity*dt.harga_barang ELSE 0 END) as total_penjualan');
        $result["total_pendapatan"] = $result["total_pendapatan"]->where('t.type', 'OUT');
        $result["total_pendapatan"] = $result["total_pendapatan"]->whereMonth('t.created_at', Carbon::now()->format('m'));
        $result["total_pendapatan"] = $result["total_pendapatan"]->whereYear('t.created_at', Carbon::now()->format('Y'));
        $result["total_pendapatan"] = $result["total_pendapatan"]->first();

        $result["total_transaksi"] = DB::table('transaksis')
            ->whereMonth('created_at', Carbon::now()->format('m'))
            ->whereYear('created_at', Carbon::now()->format('Y'))
            ->where('type', 'OUT')
            ->count();
        setlocale(LC_ALL, 'IND');
        $result["bulan_tahun"] = strftime('%B %Y');

        $result["total_pegawai"] = MasterPegawai::all()->count();

        if ($request->ajax()) {
            $result = MasterBarang::getPerhitungan();

            return  datatables()->of($result)->make(true);
        }
        return view('pages.dashboard.index', $result);
    }
}
