<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $result = MasterBarang::getBarang();

            return  datatables()->of($result)->make(true);
        }

        return view('pages.barang.index');
    }

    public function store(Request $request)
    {
        try {
            $validator = $this->validator($request->all());
            $result["code"] = 200;

            if ($validator["status"] != true) {
                $result = [
                    "error" => $validator,
                    "nama_barang" => $request->nama_barang,
                    "harga_barang" => $request->harga_barang,
                ];

                $result["code"] = 422;
            }

            if ($validator["status"]) {
                $response = MasterBarang::store($request->all());
                $result["code"] = $response["status"] ? 200 : 500;
                $result["message"] = $response["message"];
            }

            return response()->json($result, $result["code"]);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
        }
    }

    function validator($request)
    {
        $rules = [
            "nama_barang" => "required",
            "harga_barang" => "required"
        ];
        $messages = [
            "harga_barang.required" => "Harga barang tidak boleh kosong",
            "nama_barang.required" => "Nama barang tidak boleh kosong"
        ];

        if (!isset($request["kode_barang"])) {
            $rules["nama_barang"] =  "required|unique:master_barangs";
            $messages["nama_barang.unique"] =  "Nama barang sudah ada";
        }


        $validator = Validator::make($request, $rules, $messages);
        $result["status"] = true;

        if ($validator->fails()) {
            $result["status"] = false;

            if (isset($validator->getMessageBag()->messages()['nama_barang'])) {
                $result["messages"]["nama_barang"] = $validator->getMessageBag()->messages()['nama_barang'];
            }

            if (isset($validator->getMessageBag()->messages()['harga_barang'])) {
                $result["messages"]["harga_barang"] = $validator->getMessageBag()->messages()['harga_barang'];
            }
        }

        if (isset($request["kode_barang"])) {
            $barang = MasterBarang::checkBarangByKode($request["kode_barang"]);
            if (!$barang["status"]) {
                $result["status"] = false;
                $result["messages"]["kode_barang"] =  "Barang dengan kode barang {$request["kode_barang"]} tidak ada";
            } else {
                $check_barang = MasterBarang::where('nama_barang', $request['nama_barang'])
                    ->where('nama_barang', '<>', $barang["barang"]["nama_barang"])->first();
                if ($check_barang) {
                    $result["status"] = false;
                    $result["messages"]["nama_barang"] =  "Nama barang sudah ada";
                }
            }
        }

        return $result;
    }

    public function show(Request $request)
    {
        $result["code"] = 200;
        $result["barang"] = MasterBarang::getBarang($request->kode_barang);
        if (count($result["barang"]) === 0) {
            $result["code"] = 404;
            $result["message"] = "Barang dengan kode barang {$request->kode_barang} tidak ada";
        }
        return response()->json($result, $result["code"]);
    }

    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            $result["code"] = 200;
            $result["message"] = "Sukses menghapus data barang";
            $barang = MasterBarang::checkBarangByKode($request->kode_barang);
            if (!$barang["status"]) {
                $result["code"] = 404;
                $result["message"] =  "Barang dengan kode barang {$request->kode_barang} tidak ada";
            } else {
                $barang["barang"]->delete();
            }

            return response()->json($result, $result["code"]);
        }
    }
}
