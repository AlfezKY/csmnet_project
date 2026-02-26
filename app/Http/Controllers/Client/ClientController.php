<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Paket; // Wajib import model Paket
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // Tarik data paket persis kayak di web.php biar halamannya gak error
        $pakets = Paket::where('is_show', true)->take(4)->get();

        return view('client.index', compact('pakets'));
    }
}
