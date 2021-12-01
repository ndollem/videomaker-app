<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class FormRenderer extends Component
{
    public $template_id;
    public $template_variables;
    public $template_title;
    public $notification;
    public $message;

    protected $api_url;
    protected $accessToken;

    public function __construct()
    {
        $this->api_url = config('app.moovly.api_url');
        $this->accessToken = config('app.moovly.access_token');
    }

    public function mount(){
        $this->notification = "";
    }

    public function getTemplate(){
        if ($this->template_id) {
            $template = $this->__getTemplateData($this->template_id);
            $this->message = $template;

            if($template){
                //get the variables for form input
                $this->template_variables = isset($template['variables']) && !empty($template['variables']) ? $template['variables'] : [];
                $this->template_title = $template['name'];
            }
        }
    }

    public function createVideo(Request $request){

        //We can add a middleware based on requirements

        $values = [
            "external_id" => $request->external_id,
            "title" => $request->title,
            "template_variables" => $request->variable_values,
        ];

        $notifications = [];

        //it would be developed later

        if($request->notification == 'gdrive') {
            $notif = [
                  "type" => "upload-google-drive",
                  "payload" => [
                    "title" => $request->title,
                    "path" => "GOOGLE DRIVE FOLDER ID"
                  ]
                ];

            array_push($notifications, $notif);
        }

        if($request->notification == 'youtube') {
            $notif = [
                    "type" => "upload-youtube",
                    "payload" => [
                        "title" => $request->title,
                        "description" => '',
                        "playlist_id" => $request->youtube['playlist_id'],
                        "callback" => ''
                    ]
                ];

            array_push($notifications, $notif);
        }

        $requestData = [
            'template_id' => $request->template_id,
            'options' => [
                'quality' => '480p',
                'create_render' => true,
                'create_project' => true
            ],
            'values' => [$values],
            'notifications' => $notifications
        ];

        $this->message = $this->__sendTemplateData($requestData);

        return redirect()->to(route('form'));
    }

    public function render()
    {
        return view('livewire.form-renderer')->extends('layouts.app');
    }

    protected function __getTemplateData($template_id = NULL){

        if(! $template_id || ! $this->accessToken){
            return;
        }

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])
            ->get($this->api_url . 'templates/' . $template_id);

        return $response->json();
    }

    protected function __sendTemplateData($data){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type'  => 'application/json'
        ])
        ->post($this->api_url . 'jobs', $data);

        return $response->json();
    }
}
