<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormRenderer extends Component
{
    public $template_id;
    public $template_variables;
    public $template_title;
    public $notification;
    public $message;
    public $jobs;
    public $videoLists = [];

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

        $this->jobs = $this->__sendTemplateData($requestData);
        $this->message = $this->jobs;

        //we can save image with jobs, because we have to wait for status updated to finished.
        //after image saved, we can create the job for upload to youtube.

        // return redirect()->to(route('form'));

    }

    public function render()
    {
        return view('livewire.form-renderer')->extends('layouts.app');
    }

    public function getVideosData(){

        // this is response example for get status video
        // $this->jobs = [
        //     'id' => 'f4921cdb-5287-11ec-afbd-0afd511c093b',
        // ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])
        ->get($this->api_url . 'jobs/' . $this->jobs['id']);

        $data = $response->json();

        $this->message = $data;

        $videos = isset($data['videos']) && count($data['videos']) > 0 ? $data['videos'] : null;

        if($videos){
            foreach ($videos as $video) {
                if($video['status'] == 'success'){
                    $this->__downloadVideo($video['url']);
                }
            }
        }
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

    protected function __downloadVideo($url){
        $file_name = basename(strtok($url,'?'));
        $file_path = $this->jobs['id'] . '/' . $file_name;

        $storage = Storage::disk('public');

        if(! $storage->exists($file_path)){
            $storage->put($file_path, file_get_contents($url));
        }

        $this->videoLists[] = $storage->url($file_path);

    }
}
