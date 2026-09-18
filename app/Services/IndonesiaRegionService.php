<?php

namespace App\Services;

class IndonesiaRegionService
{
    /**
     * Data Lengkap 38 Provinsi beserta Kota dan Kabupaten di Seluruh Indonesia.
     */
    public static function getProvincesWithCities(): array
    {
        return [
            'Aceh' => [
                'Kota Banda Aceh', 'Kota Sabang', 'Kota Lhokseumawe', 'Kota Langsa', 'Kota Subulussalam',
                'Kab. Aceh Besar', 'Kab. Aceh Barat', 'Kab. Aceh Barat Daya', 'Kab. Aceh Jaya',
                'Kab. Aceh Selatan', 'Kab. Aceh Singkil', 'Kab. Aceh Tamiang', 'Kab. Aceh Tengah',
                'Kab. Aceh Tenggara', 'Kab. Aceh Timur', 'Kab. Aceh Utara', 'Kab. Bener Meriah',
                'Kab. Bireuen', 'Kab. Gayo Lues', 'Kab. Nagan Raya', 'Kab. Pidie', 'Kab. Pidie Jaya', 'Kab. Simeulue'
            ],
            'Sumatera Utara' => [
                'Kota Medan', 'Kota Binjai', 'Kota Tebing Tinggi', 'Kota Pematangsiantar', 'Kota Tanjungbalai',
                'Kota Sibolga', 'Kota Padangsidimpuan', 'Kota Gunungsitoli',
                'Kab. Asahan', 'Kab. Batu Bara', 'Kab. Dairi', 'Kab. Deli Serdang', 'Kab. Humbang Hasundutan',
                'Kab. Karo', 'Kab. Labuhanbatu', 'Kab. Labuhanbatu Selatan', 'Kab. Labuhanbatu Utara',
                'Kab. Langkat', 'Kab. Mandailing Natal', 'Kab. Nias', 'Kab. Nias Barat', 'Kab. Nias Selatan',
                'Kab. Nias Utara', 'Kab. Padang Lawas', 'Kab. Padang Lawas Utara', 'Kab. Pakpak Bharat',
                'Kab. Samosir', 'Kab. Serdang Bedagai', 'Kab. Simalungun', 'Kab. Tapanuli Selatan',
                'Kab. Tapanuli Tengah', 'Kab. Tapanuli Utara', 'Kab. Toba'
            ],
            'Sumatera Barat' => [
                'Kota Padang', 'Kota Bukittinggi', 'Kota Padang Panjang', 'Kota Pariaman', 'Kota Payakumbuh',
                'Kota Sawahlunto', 'Kota Solok',
                'Kab. Agam', 'Kab. Dharmasraya', 'Kab. Kepulauan Mentawai', 'Kab. Lima Puluh Kota',
                'Kab. Padang Pariaman', 'Kab. Pasaman', 'Kab. Pasaman Barat', 'Kab. Pesisir Selatan',
                'Kab. Sijunjung', 'Kab. Solok', 'Kab. Solok Selatan', 'Kab. Tanah Datar'
            ],
            'Riau' => [
                'Kota Pekanbaru', 'Kota Dumai',
                'Kab. Bengkalis', 'Kab. Indragiri Hilir', 'Kab. Indragiri Hulu', 'Kab. Kampar',
                'Kab. Kepulauan Meranti', 'Kab. Kuantan Singingi', 'Kab. Pelalawan', 'Kab. Rokan Hilir',
                'Kab. Rokan Hulu', 'Kab. Siak'
            ],
            'Kepulauan Riau' => [
                'Kota Batam', 'Kota Tanjungpinang',
                'Kab. Bintan', 'Kab. Karimun', 'Kab. Kepulauan Anambas', 'Kab. Lingga', 'Kab. Natuna'
            ],
            'Jambi' => [
                'Kota Jambi', 'Kota Sungai Penuh',
                'Kab. Batanghari', 'Kab. Bungo', 'Kab. Kerinci', 'Kab. Merangin', 'Kab. Muaro Jambi',
                'Kab. Sarolangun', 'Kab. Tanjung Jabung Barat', 'Kab. Tanjung Jabung Timur', 'Kab. Tebo'
            ],
            'Sumatera Selatan' => [
                'Kota Palembang', 'Kota Prabumulih', 'Kota Pagar Alam', 'Kota Lubuklinggau',
                'Kab. Banyuasin', 'Kab. Empat Lawang', 'Kab. Lahat', 'Kab. Muara Enim', 'Kab. Musi Banyuasin',
                'Kab. Musi Rawas', 'Kab. Musi Rawas Utara', 'Kab. Ogan Ilir', 'Kab. Ogan Komering Ilir',
                'Kab. Ogan Komering Ulu', 'Kab. Ogan Komering Ulu Selatan', 'Kab. Ogan Komering Ulu Timur',
                'Kab. Penukal Abab Lematang Ilir'
            ],
            'Kepulauan Bangka Belitung' => [
                'Kota Pangkalpinang',
                'Kab. Bangka', 'Kab. Bangka Barat', 'Kab. Bangka Selatan', 'Kab. Bangka Tengah',
                'Kab. Belitung', 'Kab. Belitung Timur'
            ],
            'Bengkulu' => [
                'Kota Bengkulu',
                'Kab. Bengkulu Selatan', 'Kab. Bengkulu Tengah', 'Kab. Bengkulu Utara', 'Kab. Kaur',
                'Kab. Kepahiang', 'Kab. Lebong', 'Kab. Mukomuko', 'Kab. Rejang Lebong', 'Kab. Seluma'
            ],
            'Lampung' => [
                'Kota Bandar Lampung', 'Kota Metro',
                'Kab. Lampung Barat', 'Kab. Lampung Selatan', 'Kab. Lampung Tengah', 'Kab. Lampung Timur',
                'Kab. Lampung Utara', 'Kab. Mesuji', 'Kab. Pesawaran', 'Kab. Pesisir Barat', 'Kab. Pringsewu',
                'Kab. Tanggamus', 'Kab. Tulang Bawang', 'Kab. Tulang Bawang Barat', 'Kab. Way Kanan'
            ],
            'DKI Jakarta' => [
                'Kota Jakarta Pusat', 'Kota Jakarta Utara', 'Kota Jakarta Barat', 'Kota Jakarta Selatan',
                'Kota Jakarta Timur', 'Kab. Kepulauan Seribu'
            ],
            'Banten' => [
                'Kota Cilegon', 'Kota Serang', 'Kota Tangerang', 'Kota Tangerang Selatan',
                'Kab. Lebak', 'Kab. Pandeglang', 'Kab. Serang', 'Kab. Tangerang'
            ],
            'Jawa Barat' => [
                'Kota Bandung', 'Kota Bekasi', 'Kota Bogor', 'Kota Cimahi', 'Kota Cirebon',
                'Kota Depok', 'Kota Sukabumi', 'Kota Tasikmalaya', 'Kota Banjar',
                'Kab. Bandung', 'Kab. Bandung Barat', 'Kab. Bekasi', 'Kab. Bogor', 'Kab. Ciamis',
                'Kab. Cianjur', 'Kab. Cirebon', 'Kab. Garut', 'Kab. Indragiri', 'Kab. Indramayu',
                'Kab. Karawang', 'Kab. Kuningan', 'Kab. Majalengka', 'Kab. Pangandaran', 'Kab. Purwakarta',
                'Kab. Subang', 'Kab. Sukabumi', 'Kab. Sumedang', 'Kab. Tasikmalaya'
            ],
            'Jawa Tengah' => [
                'Kota Semarang', 'Kota Magelang', 'Kota Pekalongan', 'Kota Salatiga', 'Kota Surakarta (Solo)', 'Kota Tegal',
                'Kab. Banjarnegara', 'Kab. Banyumas', 'Kab. Batang', 'Kab. Blora', 'Kab. Boyolali',
                'Kab. Brebes', 'Kab. Cilacap', 'Kab. Demak', 'Kab. Grobogan', 'Kab. Jepara',
                'Kab. Karanganyar', 'Kab. Kebumen', 'Kab. Kendal', 'Kab. Klaten', 'Kab. Kudus',
                'Kab. Magelang', 'Kab. Pati', 'Kab. Pekalongan', 'Kab. Pemalang', 'Kab. Purbalingga',
                'Kab. Purworejo', 'Kab. Rembang', 'Kab. Semarang', 'Kab. Sragen', 'Kab. Sukoharjo',
                'Kab. Tegal', 'Kab. Temanggung', 'Kab. Wonogiri', 'Kab. Wonosobo'
            ],
            'DI Yogyakarta' => [
                'Kota Yogyakarta', 'Kab. Bantul', 'Kab. Gunungkidul', 'Kab. Kulon Progo', 'Kab. Sleman'
            ],
            'Jawa Timur' => [
                'Kota Surabaya', 'Kota Batu', 'Kota Blitar', 'Kota Kediri', 'Kota Madiun',
                'Kota Malang', 'Kota Mojokerto', 'Kota Pasuruan', 'Kota Probolinggo',
                'Kab. Bangkalan', 'Kab. Banyuwangi', 'Kab. Bojonegoro', 'Kab. Bondowoso', 'Kab. Gresik',
                'Kab. Jember', 'Kab. Jombang', 'Kab. Kediri', 'Kab. Lamongan', 'Kab. Lumajang',
                'Kab. Madiun', 'Kab. Magetan', 'Kab. Malang', 'Kab. Mojokerto', 'Kab. Nganjuk',
                'Kab. Ngawi', 'Kab. Pacitan', 'Kab. Pamekasan', 'Kab. Pasuruan', 'Kab. Ponorogo',
                'Kab. Probolinggo', 'Kab. Sampang', 'Kab. Sidoarjo', 'Kab. Situbondo', 'Kab. Sumenep',
                'Kab. Trenggalek', 'Kab. Tuban', 'Kab. Tulungagung'
            ],
            'Bali' => [
                'Kota Denpasar', 'Kab. Badung', 'Kab. Bangli', 'Kab. Buleleng', 'Kab. Gianyar',
                'Kab. Jembrana', 'Kab. Karangasem', 'Kab. Klungkung', 'Kab. Tabanan'
            ],
            'Nusa Tenggara Barat' => [
                'Kota Mataram', 'Kota Bima', 'Kab. Bima', 'Kab. Dompu', 'Kab. Lombok Barat',
                'Kab. Lombok Tengah', 'Kab. Lombok Timur', 'Kab. Lombok Utara', 'Kab. Sumbawa', 'Kab. Sumbawa Barat'
            ],
            'Nusa Tenggara Timur' => [
                'Kota Kupang', 'Kab. Alor', 'Kab. Belu', 'Kab. Ende', 'Kab. Flores Timur', 'Kab. Kupang',
                'Kab. Lembata', 'Kab. Malaka', 'Kab. Manggarai', 'Kab. Manggarai Barat', 'Kab. Manggarai Timur',
                'Kab. Nagekeo', 'Kab. Ngada', 'Kab. Rote Ndao', 'Kab. Sabu Raijua', 'Kab. Sikka',
                'Kab. Sumba Barat', 'Kab. Sumba Barat Daya', 'Kab. Sumba Tengah', 'Kab. Sumba Timur',
                'Kab. Timor Tengah Selatan', 'Kab. Timor Tengah Utara'
            ],
            'Kalimantan Barat' => [
                'Kota Pontianak', 'Kota Singkawang', 'Kab. Bengkayang', 'Kab. Kapuas Hulu', 'Kab. Kayong Utara',
                'Kab. Ketapang', 'Kab. Kubu Raya', 'Kab. Landak', 'Kab. Melawi', 'Kab. Mempawah',
                'Kab. Sambas', 'Kab. Sanggau', 'Kab. Sekadau', 'Kab. Sintang'
            ],
            'Kalimantan Tengah' => [
                'Kota Palangka Raya', 'Kab. Barito Selatan', 'Kab. Barito Timur', 'Kab. Barito Utara',
                'Kab. Gunung Mas', 'Kab. Kapuas', 'Kab. Katingan', 'Kab. Kotawaringin Barat',
                'Kab. Kotawaringin Timur', 'Kab. Lamandau', 'Kab. Murung Raya', 'Kab. Pulang Pisau',
                'Kab. Sukamara', 'Kab. Seruyan'
            ],
            'Kalimantan Selatan' => [
                'Kota Banjarmasin', 'Kota Banjarbaru', 'Kab. Balangan', 'Kab. Banjar', 'Kab. Barito Kuala',
                'Kab. Hulu Sungai Selatan', 'Kab. Hulu Sungai Tengah', 'Kab. Hulu Sungai Utara',
                'Kab. Kotabaru', 'Kab. Tabalong', 'Kab. Tanah Bumbu', 'Kab. Tanah Laut', 'Kab. Tapin'
            ],
            'Kalimantan Timur' => [
                'Kota Samarinda', 'Kota Balikpapan', 'Kota Bontang', 'Kab. Berau', 'Kab. Kutai Barat',
                'Kab. Kutai Kartanegara', 'Kab. Kutai Timur', 'Kab. Mahakam Ulu', 'Kab. Paser', 'Kab. Penajam Paser Utara'
            ],
            'Kalimantan Utara' => [
                'Kota Tarakan', 'Kab. Bulungan', 'Kab. Malinau', 'Kab. Nunukan', 'Kab. Tana Tidung'
            ],
            'Sulawesi Utara' => [
                'Kota Manado', 'Kota Bitung', 'Kota Kotamobagu', 'Kota Tomohon',
                'Kab. Bolaang Mongondow', 'Kab. Bolaang Mongondow Selatan', 'Kab. Bolaang Mongondow Timur',
                'Kab. Bolaang Mongondow Utara', 'Kab. Kepulauan Sangihe', 'Kab. Kepulauan Siau Tagulandang Biaro',
                'Kab. Kepulauan Talaud', 'Kab. Minahasa', 'Kab. Minahasa Selatan', 'Kab. Minahasa Tenggara',
                'Kab. Minahasa Utara'
            ],
            'Gorontalo' => [
                'Kota Gorontalo', 'Kab. Boalemo', 'Kab. Bone Bolango', 'Kab. Gorontalo',
                'Kab. Gorontalo Utara', 'Kab. Pohuwato'
            ],
            'Sulawesi Tengah' => [
                'Kota Palu', 'Kab. Banggai', 'Kab. Banggai Kepulauan', 'Kab. Banggai Laut', 'Kab. Buol',
                'Kab. Donggala', 'Kab. Morowali', 'Kab. Morowali Utara', 'Kab. Parigi Moutong',
                'Kab. Poso', 'Kab. Sigi', 'Kab. Tojo Una-Una', 'Kab. Toli-Toli'
            ],
            'Sulawesi Barat' => [
                'Kab. Majene', 'Kab. Mamasa', 'Kab. Mamuju', 'Kab. Mamuju Tengah', 'Kab. Pasangkayu', 'Kab. Polewali Mandar'
            ],
            'Sulawesi Selatan' => [
                'Kota Makassar', 'Kota Palopo', 'Kota Parepare',
                'Kab. Bantaeng', 'Kab. Barru', 'Kab. Bone', 'Kab. Bulukumba', 'Kab. Enrekang',
                'Kab. Gowa', 'Kab. Jeneponto', 'Kab. Kepulauan Selayar', 'Kab. Luwu', 'Kab. Luwu Timur',
                'Kab. Luwu Utara', 'Kab. Maros', 'Kab. Pangkajene dan Kepulauan', 'Kab. Pinrang',
                'Kab. Sidenreng Rappang', 'Kab. Sinjai', 'Kab. Soppeng', 'Kab. Takalar',
                'Kab. Tana Toraja', 'Kab. Toraja Utara', 'Kab. Wajo'
            ],
            'Sulawesi Tenggara' => [
                'Kota Kendari', 'Kota Baubau',
                'Kab. Bombana', 'Kab. Buton', 'Kab. Buton Selatan', 'Kab. Buton Tengah', 'Kab. Buton Utara',
                'Kab. Kolaka', 'Kab. Kolaka Timur', 'Kab. Kolaka Utara', 'Kab. Konawe', 'Kab. Konawe Kepulauan',
                'Kab. Konawe Selatan', 'Kab. Konawe Utara', 'Kab. Muna', 'Kab. Muna Barat', 'Kab. Wakatobi'
            ],
            'Maluku' => [
                'Kota Ambon', 'Kota Tual',
                'Kab. Buru', 'Kab. Buru Selatan', 'Kab. Kepulauan Aru', 'Kab. Kepulauan Tanimbar',
                'Kab. Maluku Barat Daya', 'Kab. Maluku Tengah', 'Kab. Maluku Tenggara',
                'Kab. Seram Bagian Barat', 'Kab. Seram Bagian Timur'
            ],
            'Maluku Utara' => [
                'Kota Ternate', 'Kota Tidore Kepulauan',
                'Kab. Halmahera Barat', 'Kab. Halmahera Tengah', 'Kab. Halmahera Timur',
                'Kab. Halmahera Selatan', 'Kab. Halmahera Utara', 'Kab. Kepulauan Sula',
                'Kab. Pulau Morotai', 'Kab. Pulau Taliabu'
            ],
            'Papua' => [
                'Kota Jayapura', 'Kab. Biak Numfor', 'Kab. Jayapura', 'Kab. Keerom',
                'Kab. Kepulauan Yapen', 'Kab. Mamberamo Raya', 'Kab. Sarmi', 'Kab. Supiori', 'Kab. Waropen'
            ],
            'Papua Barat' => [
                'Kab. Fakfak', 'Kab. Kaimana', 'Kab. Manokwari', 'Kab. Manokwari Selatan',
                'Kab. Pegunungan Arfak', 'Kab. Teluk Bintuni', 'Kab. Teluk Wondama'
            ],
            'Papua Selatan' => [
                'Kab. Asmat', 'Kab. Boven Digoel', 'Kab. Mappi', 'Kab. Merauke'
            ],
            'Papua Tengah' => [
                'Kab. Deiyai', 'Kab. Dogiyai', 'Kab. Intan Jaya', 'Kab. Mimika',
                'Kab. Nabire', 'Kab. Paniai', 'Kab. Puncak', 'Kab. Puncak Jaya'
            ],
            'Papua Pegunungan' => [
                'Kab. Jayawijaya', 'Kab. Lanny Jaya', 'Kab. Mamberamo Tengah', 'Kab. Nduga',
                'Kab. Pegunungan Bintang', 'Kab. Tolikara', 'Kab. Yahukimo', 'Kab. Yalimo'
            ],
            'Papua Barat Daya' => [
                'Kota Sorong', 'Kab. Maybrat', 'Kab. Raja Ampat', 'Kab. Sorong',
                'Kab. Sorong Selatan', 'Kab. Tambrauw'
            ]
        ];
    }

    /**
     * Daftar nama seluruh provinsi di Indonesia yang diurutkan alfabetis.
     */
    public static function getProvinces(): array
    {
        $provinces = array_keys(self::getProvincesWithCities());
        sort($provinces);
        return $provinces;
    }
}
