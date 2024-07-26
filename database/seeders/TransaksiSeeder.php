<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = file_get_contents('database/seeders/json/Transaksi.json');
        $data = (array)json_decode($data);
        $data = array_map(function ($data) {
            return (array) $data;
        }, $data);
        DB::beginTransaction();
        try {
            DB::table('transaksis')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
