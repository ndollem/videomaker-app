<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->api_url = 'https:/api.moovly.com/';
        // $this->api_url = config('app.moovly.api_url');
        $this->accessToken = config('app.moovly.access_token');
    }

    public function index()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])
        ->baseUrl($this->api_url)
        ->get('project/v1/users/me/projects');

        dd($response->json());
    }

    public function show($project_id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])
        ->baseUrl($this->api_url)
        ->get('project/v1/projects/' . $project_id);

        dd($response->json());
    }
}
