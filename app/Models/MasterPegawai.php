<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterPegawai extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function store($request)
    {
        $result["status"] = true;
        try {
            if (!isset($request["id"])) {
                $user = new User;
                $user->name = $request["nama_pegawai"];
                $user->username = $request["username"];
                $user->email = $request["email"];
                $user->password = bcrypt($request["password"]);
                $user->role = 'pegawai';
                $user->save();
                $id = $user->id;
                self::create(["user_id" => $id, "alamat" => $request['alamat_pegawai']]);
                $result["message"] = "Sukses menambahkan pegawai baru";
            } else {
                $user = User::where('id', $request["id"])->first();
                $user->name = $request["nama_pegawai"];
                $user->username = $request["username"];
                $user->email = $request["email"];
                $user->password = bcrypt($request["password"]);
                $user->save();
                $pegawai = self::where('user_id', $request["id"])->first();
                $pegawai->alamat = $request['alamat_pegawai'];
                $pegawai->save();

                $result["message"] = "Sukses mengubah data pegawai";
            }
        } catch (\Exception $ex) {
            $result["status"] = false;
            $result["message"] = $ex->getMessage();
        }

        return $result;
    }
}
