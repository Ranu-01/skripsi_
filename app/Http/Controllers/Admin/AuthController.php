<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $result = User::with(['pegawais'])->where('role', 'pegawai')->get();
            return datatables()->of($result)->make(true);
        }

        return view('pages.pegawai.index');
    }

    public function store(Request $request)
    {
        try {
            $validator = $this->validator($request->all());
            $result["code"] = 200;

            if ($validator["status"] != true) {
                $result = [
                    "error" => $validator
                ];

                $result["code"] = 422;
            }

            if ($validator["status"]) {
                $response = MasterPegawai::store($request->all());
                $result["code"] = $response["status"] ? 200 : 500;
                $result["message"] = $response["message"];
            }

            return response()->json($result, $result["code"]);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
        }
    }

    public function validator($request)
    {
        $rules = [
            "nama_pegawai" => "required",
            "alamat_pegawai" => "required",
            "username" => "required|unique:users,username",
            "email" => "required|unique:users,email",
            "password" => "required"
        ];
        $messages = [
            "nama_pegawai.required" => "Nama Pegawai tidak boleh kosong",
            "alamat_pegawai.required" => "Alamat Pegawai tidak boleh kosong",
            "username.required" => "Username tidak boleh kosong",
            "email.required" => "Email tidak boleh kosong",
            "password.required" => "Password tidak boleh kosong",
            "username.unique" => "Username sudah dipakai",
            "email.unique" => "Email sudah dipakai",
        ];

        $validator = Validator::make($request, $rules, $messages);
        $result["status"] = true;

        if ($validator->fails()) {
            $result["status"] = false;

            if (isset($validator->getMessageBag()->messages()['nama_pegawai'])) {
                $result["messages"]["nama_pegawai"] = $validator->getMessageBag()->messages()['nama_pegawai'];
            }

            if (isset($validator->getMessageBag()->messages()['alamat_pegawai'])) {
                $result["messages"]["alamat_pegawai"] = $validator->getMessageBag()->messages()['alamat_pegawai'];
            }
            if (isset($validator->getMessageBag()->messages()['username'])) {
                $result["messages"]["username"] = $validator->getMessageBag()->messages()['username'];
            }
            if (isset($validator->getMessageBag()->messages()['email'])) {
                $result["messages"]["email"] = $validator->getMessageBag()->messages()['email'];
            }
            if (isset($validator->getMessageBag()->messages()['password'])) {
                $result["messages"]["password"] = $validator->getMessageBag()->messages()['password'];
            }
        }

        return $result;
    }

    public function destroy(Request $request)
    {
        $result["code"] = 200;
        $result["message"] = "Sukses menghapus data barang";
        $user = User::where('id', $request->id)->first();
        if (!$user) {
            $result["code"] = 404;
            $result["message"] =  "Pegawai dengan nama {$user->name} tidak ada";
        } else {
            MasterPegawai::where('user_id', $user->id)->delete();
            $user->delete();
        }
        return response()->json($result, $result["code"]);
    }

    public function show(Request $request)
    {
        $result["code"] = 200;
        $result["pegawai"] = User::with(['pegawais'])->where('id', $request->id)->first();
        if (!$result["pegawai"]) {
            $result["code"] = 404;
            $result["message"] = "Pegawai dengan nama {$result["pegawai"]->name} tidak ada";
        }
        return response()->json($result, $result["code"]);
    }

    public function indexLogin()
    {
        return view('pages.auth.index');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->intended('/');
        } else {
            return redirect()->route('auth.login')->with('msg', 'Akun tidak ditemukan, periksa kembali username/password Anda');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect(route('auth.login'));
    }
}
