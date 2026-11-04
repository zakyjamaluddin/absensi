<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Halaman Kebijakan Privasi (Privacy Policy)
     */
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    /**
     * Halaman Ketentuan Layanan (Terms of Service)
     */
    public function termsOfService()
    {
        return view('legal.terms-of-service');
    }
}
