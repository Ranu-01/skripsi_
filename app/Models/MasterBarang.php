<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterBarang extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function store($request)
    {
        $result["status"] = true;
        try {
            if (!isset($request["kode_barang"])) {
                $kode_barang = self::generateKodeBarang();
                self::create(["kode_barang" => $kode_barang, "nama_barang" => $request['nama_barang'], "harga_barang" => $request['harga_barang'], 'created_by' => Auth::user()->id]);
                $result["message"] = "Sukses menambahkan barang baru";
            } else {
                $barang = self::where('kode_barang', $request["kode_barang"])->first();
                $barang->nama_barang = $request['nama_barang'];
                $barang->harga_barang = $request['harga_barang'];
                $barang->save();
                $result["message"] = "Sukses mengubah data barang";
            }
        } catch (\Exception $ex) {
            $result["status"] = false;
            $result["message"] = $ex->getMessage();
        }

        return $result;
    }

    public function detail_transaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public static function generateKodeBarang()
    {
        $barang_id = DB::table('master_barangs')->max('kode_barang');
        $addZero = '';
        $barang_id = str_replace("BR", "", $barang_id);
        $barang_id = (int) $barang_id + 1;
        $incrementBarangId = $barang_id;

        if (strlen($barang_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($barang_id) == 2) {
            $addZero = "000";
        } elseif (strlen($barang_id) == 3) {
            $addZero = "00";
        } elseif (strlen($barang_id) == 4) {
            $addZero = "0";
        }

        $newBarangId = "BR" . $addZero . $incrementBarangId;
        return $newBarangId;
    }

    public static function getBarang($kode_barang = null, $dashboard = false)
    {
        // $select = "mb.kode_barang, mb.nama_barang, mb.harga_barang, ";
        // $select .= "(SUM(CASE WHEN t.type = 'IN' THEN dt.quantity ELSE 0 END)";
        // $select .= "- SUM(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END)) as stok";
        // $barang = DB::table('master_barangs as mb')
        //     ->leftJoin('detail_transaksis as dt', 'mb.id', '=', 'dt.master_barang_id')
        //     ->leftJoin('transaksis as t', 'dt.transaksi_id', '=', 't.id')
        //     ->selectRaw($select);

        $select = "SELECT kode_barang, nama_barang, harga_barang, sum(stok) as stok,sum(ss) as safety_stok, sum(rop) as rop ";
        $select .= "FROM ";
        $select .= "(SELECT mb.kode_barang, mb.nama_barang , mb.harga_barang,";
        $select .= "(SUM(CASE WHEN t.type = 'IN' THEN dt.quantity ELSE 0 END) - ";
        $select .= "SUM(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END)) as stok, 0 as ss, 0 as rop ";
        $select .= "FROM master_barangs as mb ";
        $select .= "LEFT JOIN detail_transaksis as dt ON mb.id = dt.master_barang_id ";
        $select .= "LEFT JOIN transaksis as t on dt.transaksi_id = t.id ";
        if ($kode_barang) {
            $select .= "WHERE mb.kode_barang = '{$kode_barang}' ";
        }
        $select .= "GROUP BY mb.kode_barang, mb.nama_barang , mb.harga_barang ";
        $select .= "UNION ";
        $select .= "SELECT mb.kode_barang, mb.nama_barang , mb.harga_barang, 0 as stok, ";
        $select .= "((MAX(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END) - AVG(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END))* 3) as ss, ";
        $select .= "(((MAX(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END) - AVG(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END)) * 3) + (3 * (SUM(CASE WHEN t.type = 'OUT' THEN dt.quantity ELSE 0 END)/365))) as rop ";
        $select .= "FROM master_barangs as mb ";
        $select .= "LEFT JOIN detail_transaksis as dt ON mb.id = dt.master_barang_id ";
        $select .= "LEFT JOIN transaksis as t on dt.transaksi_id = t.id ";
        $select .= "WHERE t.type = 'OUT' ";
        if ($kode_barang) {
            $select .= "AND mb.kode_barang = '{$kode_barang}' ";
        }
        $select .= "GROUP BY mb.kode_barang, mb.nama_barang , mb.harga_barang";
        $select .= ")as sum ";
        if ($dashboard) {
            $select .= "WHERE stok <= rop ";
        }
        $select .= "GROUP BY kode_barang, nama_barang , harga_barang ";
        if ($dashboard) {
            $select .= "ORDER BY rop, stok ASC ";
        }
        $barang = DB::select($select);


        return $barang;
    }

    public static function checkBarangByKode($kode_barang)
    {
        $result["status"] = true;
        $result["barang"] = MasterBarang::where('kode_barang', $kode_barang)->first();
        if (!$result["barang"]) {
            $result["status"] = false;
        }

        return $result;
    }

    public static function getPerhitungan()
    {
        // return DB::table("detail_transaksis as dt")
        //     ->selectRaw(
        //         "STDDEV(dt.quantity) AS deviation, STDDEV(dt.quantity)*1.65 AS safety_stock" .
        //             ", AVG(dt.quantity) AS avg_peritem, DATE_FORMAT(t.created_at, '%Y-%m') AS sale_date" .
        //             ",(AVG(dt.quantity) * 1 + (STDDEV(dt.quantity) *1.65)) AS min" .
        //             ", (2*(AVG(dt.quantity) * 1 + (STDDEV(dt.quantity) *1.65))) AS max" .
        //             ", (2*(AVG(dt.quantity) * 1 + (STDDEV(dt.quantity) *1.65))) - (AVG(dt.quantity) * 1 + (STDDEV(dt.quantity) *1.65)) AS Q"
        //     )
        //     ->join("transaksis as t", "t.id", "=", "dt.transaksi_id")
        //     ->where("t.type", "=", "OUT")
        //     ->groupByRaw("DATE_FORMAT(t.created_at, '%Y-%m')")
        //     ->orderByRaw("DATE_FORMAT(t.created_at, '%Y-%m') DESC")
        //     ->get();

        return DB::table('detail_transaksis AS dt')
            ->select([
                'dt.master_barang_id AS item_id',
                DB::raw('ROUND(SUM(dt.quantity) / 30) * 1 AS safety_stock'),
                DB::raw('AVG(dt.quantity) AS avg_peritem'),
                DB::raw('DATE_FORMAT(t.created_at, "%Y%m") AS sale_date'),
                DB::raw('(AVG(dt.quantity) * 1 + (ROUND(SUM(dt.quantity) / 30) * 1)) AS min'),
                DB::raw('(2 * (AVG(dt.quantity) * 1 + (ROUND(SUM(dt.quantity) / 30) * 1))) AS max'),
                DB::raw('(2 * (AVG(dt.quantity) * 1 + (ROUND(SUM(dt.quantity) / 30) * 1))) - (AVG(dt.quantity) * 1 + (ROUND(SUM(dt.quantity) / 30) * 1)) AS Q'),
            ])
            ->join('transaksis AS t', 't.id', '=', 'dt.transaksi_id')
            ->join('master_barangs AS mb', 'mb.id', '=', 'dt.master_barang_id')
            ->where('t.type', '=', 'OUT')
            ->groupBy(DB::raw('DATE_FORMAT(t.created_at, "%Y%m")'), 'mb.id')
            ->orderBy('mb.id', 'DESC')
            ->orderBy(DB::raw('DATE_FORMAT(t.created_at, "%Y%m")'), 'DESC')
            ->get();



        // return DB::table("detail_transaksis as dt")
        //     ->selectRaw(
        //         "dt.master_barang_id AS item_id".
        //         "(round(SUM(dt.quantity) / 30) * 1) AS safety_stock" .
        //             ", AVG(dt.quantity) AS avg_peritem, DATE_FORMAT(t.created_at, '%Y-%m') AS sale_date" .
        //             ",(AVG(dt.quantity) * 1 + (round(SUM(dt.quantity) / 30) * 1)) AS min" .
        //             ", (2*(AVG(dt.quantity) * 1 + (round(SUM(dt.quantity) / 30) * 1))) AS max" .
        //             ", (2*(AVG(dt.quantity) * 1 + (round(SUM(dt.quantity) / 30) * 1))) - (AVG(dt.quantity) * 1 + (round(SUM(dt.quantity) / 30) * 1)) AS Q"
        //     )
        //     ->join("transaksis as t", "t.id", "=", "dt.transaksi_id")
        //     ->join ("master_barangs mb" , "mb.id", "=", "dt.master_barang_id")
        //     ->where("t.type", "=", "OUT")
        //     ->groupByRaw("DATE_FORMAT(t.created_at, '%Y-%m'),mb.id")
        //     ->orderByRaw("DATE_FORMAT(t.created_at, '%Y-%m'),mb.id DESC")
        //     ->get();
    }
}
