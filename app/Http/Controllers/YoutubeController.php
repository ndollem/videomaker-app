<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleService;

class YoutubeController extends Controller
{
    public function index(Request $request)
    {

        $google = new GoogleService('http://localhost:8000/youtube', null);
        $auth_url =  $google->auth_url();

        if ($request->get('code')) {
            $token = $google->fetchAccessToken($request->get('code'));
            $request->session()->put('youtube-token', $token);

            return redirect()->route('youtube');
            dump($token);

        }
            // dump($google->oauth->userinfo->get());
        // dump(session('youtube-token'));


        return view('pages.google', compact('auth_url'));
    }
}
