<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | Lintang CI4'
        ];
        return view('pages/home', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Me'
        ];
        return view('pages/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'alamat' => [
                [
                    'tipe' => 'rumah',
                    'alamat' => 'jalan asdf no. 123',
                    'kota' => 'Surabaya'
                ],
                [
                    'tipe' => 'kantor',
                    'alamat' => 'office 365',
                    'kota' => 'Malang'
                ]
            ]
        ];
        return view('pages/contact', $data);
    }
}
