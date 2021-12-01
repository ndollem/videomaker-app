<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

use App\Services\GoogleService;
use Google\Service\YouTube as YT;

class Youtube extends Component
{
    use WithFileUploads;

    public $auth_url;

    public $title;

    public $description;

    public $tags;

    public $category;

    public $status;

    public $video;


    protected $rules = [
        'title' => 'required',
        'description' => 'required',
        'tags' => 'nullable'
    ];

    public function render()
    {
        $data = array();

        $google = new GoogleService('http://localhost:8000/youtube', null);

        if(session('youtube-token')) {
            $data['videoCategories'] = $google->getVideoCategories();
        }

        return view('livewire.youtube', $data);
    }

    public function submit()
    {
        $validated = $this->validate();

        $snippet = new YT\VideoSnippet();
        $snippet->setTitle($this->title);
        $snippet->setDescription($this->description);
        $snippet->setTags(explode(',',$this->tags));
        $snippet->setCategoryId($this->category);

        $status = new YT\VideoStatus();
        $status->privacyStatus = $this->status;

        $video = new YT\Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // chunk size
        $chunkSizeBytes = 1 * 1024 * 1024;

        $youtube = (new GoogleService())->youtube();
        $insertRequest = $youtube->videos->insert(
            "status,snippet",
            $video,
            array(
                'data' => file_get_contents($this->video->path()),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart'
            )
        );

        dump($insertRequest->getSnippet(), $insertRequest->getStatus());

        // Create a MediaFileUpload object for resumable uploads.
        // $media = new \Google\Http\MediaFileUpload(
        //     (new GoogleService())->google,
        //     $insertRequest,
        //     'video/*',
        //     null,
        //     true,
        //     $chunkSizeBytes
        // );
        // $media->setFileSize(filesize($this->video->path()));

        // // Read the media file and upload it chunk by chunk.
        // $status = false;
        // $handle = fopen($this->video->path(), "rb");
        // while (!$status && !feof($handle)) {
        //     $chunk = fread($handle, $chunkSizeBytes);
        //     $status = $media->nextChunk($chunk);
        // }

        // fclose($handle);

        dump($status);
        // dump(($this->video->path()));
    }
}
