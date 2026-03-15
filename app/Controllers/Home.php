<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'pageTitle' => 'Byte-Sized Bakes',
        ];

        return view('templates/bsb_header', $data)
            . view('landing/bsb_landing', $data)
            . view('templates/bsb_footer', $data);
    }
}