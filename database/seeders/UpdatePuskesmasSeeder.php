<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puskesmas;

class UpdatePuskesmasSeeder extends Seeder
{
    public function run()
    {
        $puskesmas = [
            'Puskesmas Terminal' => ['alamat' => 'Jl. Pramuka Komplek Terminal Induk Km 6', 'lat' => -3.331991, 'lng' => 114.627351],
            'Puskesmas Sungai Bilu' => ['alamat' => 'Jl. Veteran Sungai Bilu', 'lat' => -3.319766, 'lng' => 114.606245],
            'Puskesmas Pekapuran Raya' => ['alamat' => 'Jl. Pekapuran Raya', 'lat' => -3.336495, 'lng' => 114.611283],
            'Puskesmas Cempaka Putih' => ['alamat' => 'Jl. Simpang Belitung', 'lat' => -3.308722, 'lng' => 114.582855],
            'Puskesmas 9 November' => ['alamat' => 'Jl. Keramat Raya No. 128', 'lat' => -3.315530, 'lng' => 114.608035],
            'Puskesmas Karang Mekar' => ['alamat' => 'Jl. Ratu Zaleha', 'lat' => -3.327891, 'lng' => 114.609112],
            'Puskesmas Pekauman' => ['alamat' => 'Jl. K.S. Tubun', 'lat' => -3.333887, 'lng' => 114.584345],
            'Puskesmas Beruntung Raya' => ['alamat' => 'Jl. A. Yani Km. 6', 'lat' => -3.342111, 'lng' => 114.620002],
            'Puskesmas Kelayan Timur' => ['alamat' => 'Jl. Kelayan B', 'lat' => -3.344445, 'lng' => 114.589112],
            'Puskesmas Pemurus Dalam' => ['alamat' => 'Jl. Sepakat', 'lat' => -3.350123, 'lng' => 114.618999],
            'Puskesmas Kelayan Dalam' => ['alamat' => 'Jl. Kelayan A', 'lat' => -3.337111, 'lng' => 114.590222],
            'Puskesmas Mantuil' => ['alamat' => 'Jl. Tembus Mantuil', 'lat' => -3.360111, 'lng' => 114.575000],
            'Puskesmas Pemurus Baru' => ['alamat' => 'Jl. Gerilya', 'lat' => -3.348888, 'lng' => 114.599999],
            'Puskesmas Sungai Jingah' => ['alamat' => 'Jl. Sungai Jingah', 'lat' => -3.305555, 'lng' => 114.600111],
            'Puskesmas Alalak Tengah' => ['alamat' => 'Jl. Alalak Tengah', 'lat' => -3.280111, 'lng' => 114.570222],
            'Puskesmas Kayu Tangi' => ['alamat' => 'Jl. Cemara Raya', 'lat' => -3.295111, 'lng' => 114.585333],
            'Puskesmas Sungai Andai' => ['alamat' => 'Jl. Padat Karya', 'lat' => -3.290222, 'lng' => 114.615444],
            'Puskesmas Alalak Selatan' => ['alamat' => 'Jl. Alalak Selatan RT 4 No 8', 'lat' => -3.285333, 'lng' => 114.575555],
            'Puskesmas Kuin Raya' => ['alamat' => 'Jl. Kuin Selatan', 'lat' => -3.300444, 'lng' => 114.570666],
            'Puskesmas Basirih Baru' => ['alamat' => 'Jl. Tanjung Keramat', 'lat' => -3.340555, 'lng' => 114.565777],
            'Puskesmas Banjarmasin Indah' => ['alamat' => 'Jl. Barito Hilir RT 17', 'lat' => -3.325666, 'lng' => 114.560888],
            'Puskesmas Pelambuan' => ['alamat' => 'Jl. Barito Hulu', 'lat' => -3.315777, 'lng' => 114.570999],
            'Puskesmas Teluk Tiram' => ['alamat' => 'Jl. Teluk Tiram Darat', 'lat' => -3.330888, 'lng' => 114.575111],
            'Puskesmas Sungai Mesa' => ['alamat' => 'Jl. Pahlawan', 'lat' => -3.315999, 'lng' => 114.595222],
            'Puskesmas Cempaka' => ['alamat' => 'Jl. Cempaka Besar No 1', 'lat' => -3.320111, 'lng' => 114.585333],
            'Puskesmas Teluk Dalam' => ['alamat' => 'Jl. Sutoyo S', 'lat' => -3.325222, 'lng' => 114.580444],
            'Puskesmas Gadang Hanyar' => ['alamat' => 'Jl. Aes Nasution', 'lat' => -3.322333, 'lng' => 114.595555],
        ];

        foreach(Puskesmas::all() as $p) {
            if(isset($puskesmas[$p->nama_puskesmas])) {
                $p->alamat = $puskesmas[$p->nama_puskesmas]['alamat'];
                $p->latitude = $puskesmas[$p->nama_puskesmas]['lat'];
                $p->longitude = $puskesmas[$p->nama_puskesmas]['lng'];
                $p->save();
            }
        }
    }
}
