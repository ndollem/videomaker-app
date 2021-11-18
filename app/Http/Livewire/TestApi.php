<?php

namespace App\Http\Livewire;

use Livewire\Component;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;

class TestApi extends Component
{
    function createVideoOri()
    {
        # install composer
        #require __DIR__ . '/vendor/autoload.php';

        $clients = [
            ['name' => 'Bill Gates'],
            //['name' => 'Jeff Besos'],
            //['name' => 'Elon Musk'],
        ];

        $projectTemplateId = 'someId';
        $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJNb292bHkiLCJhdWQiOiJwZXJzb25hbC1hY2Nlc3MtdG9rZW5zIiwianRpIjoiNTJjY2U1ZTM4ZDE0Mzk3ZjA1Yjg1YjcyZTg5NTM1OGNhMTc0NTFlNDdkMTY0YmE0NGUzOGUxNTk2NGQzOGVhNmU4MjZjZWY5YzM0NGU0YzAiLCJpYXQiOjE2MzQ3ODA0OTguMDM2MDA2LCJleHAiOjE2NjYzMTY0OTguMDIzNzEsInN1YiI6IjJlNGU0MDcxLTJmZjItMTFlYy1iNWVmLTBhOWQ0ZjkxZjQ2ZiIsInVzZXJfaWQiOjExMDI3NTQwMjIsInJvbGVzIjpbIlJPTEVfUEVSU09OQUxfQUNDRVNTX1RPS0VOIl19.Pq-TRQyUpeBuVeGuW_0GbjZmQ2aYk3OhFwEeb05IEIkmCfVcuxIuyrw56pmbKI3Vrjfus_n-_1FCjmSyzTGuwGIV1NLMCAOuSY_y3Nl4T_APDHzI5phLoIJZeRaGksDMqL1Yh1VWtgvn8SdDXaJp1SJ0Brj4PUQ8LzhGRNIYJTvy88jyxQODfktVmhEzFaEb3jdW9tuTdzcQIs2nfKbwb1emLH6ZAUGxwgvZBqhjoSoe4yC6EzQYnwkWbAyJnfq09MPpyOWBgAHGKY50okHOmo5P5TlE7Gki7g2WLyf3G102Eukk0icxw7MNTOwv7yv_WKoXsvkbfudo5qI7ci6Xiw';

        $httpClient = new GuzzleHttp\Client();

        $response = $httpClient->post('https://api.moovly.com/generator/v1/templates', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'form_params' => [
                'moov_id' => $projectTemplateId
            ]
        ]);

        $template = json_decode($response->getBody()->getContents(), true);

        $templateId = $template['id'];
        $variables = $template['variables'];

        $values = [];

        $nameVariable = $variables[0];

        foreach ($clients as $key => $client) {
            $values[] = [
                "external_id" => $key,
                "title" => 'Moov for ' . $client['name'],
                "template_variables" => [
                    $nameVariable['id'] => $client['name']
                ]
            ];
        }

        $requestData = [
            'template_id' => $templateId,
            'options' => [
                'quality' => '480p'
            ],
            'values' => $values
        ];

        $response = $httpClient->post('https://api.moovly.com/generator/v1/jobs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json'
            ],
            'json' => $requestData
        ]);

        $jobs = json_decode($response->getBody()->getContents(), true);

        $jobId = $jobs['id'];

        do {
            $response = $httpClient->get('https://api.moovly.com/generator/v1/jobs/' . $jobId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ]
            ]);

            $jobsStates = array_map(function (array $job) {
                return $job['status'] === 'finished';
            }, $jobs['videos']);

            $finishedUrls = array_map(function (array $job) {
                return $job['url'];
            }, $jobs['videos']);
            sleep(30);
        } while (in_array(false, $jobsStates, true));

        echo 'Video urls ' . implode(', ', $finishedUrls);
    }

    function getTemplate()
    {
        $projectTemplateId = 'edf354c4-36d1-11ec-bee9-0afd511c093b';
        $projectTemplateId = '13e4e830-59e3-11e9-b2d9-0a0ccd2cb430';
        $accessToken = env('MOVLY_TOKEN', false);

        if($accessToken)
        {
            //$httpClient = new GuzzleHttp\Client();
            $httpClient = new GuzzleClient();

            $response = $httpClient->get('https://api.moovly.com/generator/v1/templates/'.$projectTemplateId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            $template = json_decode($response->getBody()->getContents(), true);

            $templateId = $template['id'];
            $variables = $template['variables'];
            
        }else{
            $template = ['err'=>'invalid token'];
        }

        dd($template);
    }

    function createVideo()
    {
        
        $post = [
            'title' => '95 Ucapan Terima Kasih untuk Orang Tua, Cerminkan Anak yang Berbakti & Penuh Kasih',
            'thumb' => 'https://cdns.klimg.com/merdeka.com/i/w/news/2021/11/18/1377243/670x335/95-ucapan-terima-kasih-untuk-orang-tua-cerminkan-anak-yang-berbakti-penuh-kasih.jpg',
            'image clip 1' => 'https://cdns.klimg.com/merdeka.com/i/w/foto/2016/07/02/350887/t/ilustrasi-ibu-dan-anak-di-hari-pernikahan-001-tantri-setyorini.jpg',
            'text clip 1' => 'Setiap anak sebaiknya sering memberikan ucapan terima kasih untuk orang tua',
            'image clip 2' => 'https://cdns.klimg.com/merdeka.com/i/w/foto/2016/07/14/352815/t/ilustrasi-ayah-dan-anak-007-indra-cahya.jpg',
            'text clip 2' => 'Lantas bagaimana ucapan terima kasih untuk orang tua? Melansir dari merdeka.com, Kamis (18/11), simak ulasan informasinya di merdeka.com',
            'logo' => 'https://assets.moovly.com/converted/images/image-7179bb2deb13dddb45e2194f84e683e7.png',
            'url' => 'merdeka.com'
        ];

        $value = [
            "external_id" => "Template News Update with Image v.2",
            "title" => $post['title'],
            "template_variables" => [
                '278f1ebc-36f2-11ec-bee9-0afd511c093b' => 'thumb',
                '4246e9a6-36de-11ec-bee9-0afd511c093b' => 'image clip 1',
                '4246f429-36de-11ec-bee9-0afd511c093b' => 'text clip 1',
                '4246fc87-36de-11ec-bee9-0afd511c093b' => 'image clip 2',
                '424704b1-36de-11ec-bee9-0afd511c093b' => 'text clip 2',
                '95927113-36f2-11ec-bee9-0afd511c093b' => 'logo',
                '9592925d-36f2-11ec-bee9-0afd511c093b' => 'url'
            ],
            "notifications" => [
                [
                  "type" => "upload-google-drive",
                  "payload" => [
                    "title" => $post['title'],
                    "path" => "1HQi4sFrSjCnJ4MOLGUqKKE9_Om8036WV"
                  ]
                ]
            ]
        ];
        

        foreach($value['template_variables'] as $index=>$name)
        {
            if($post[$name]){
                if( substr( strtolower($name), 0, 4)=='text' ){
                    $post[$name] = wordwrap($post[$name], 65, "\n");
                }
                $value['template_variables'][$index] = $post[$name];
            }
        }

        $values = [];
        $values[] = $value;
        //dd($values);
        

        $projectTemplateId = 'edf354c4-36d1-11ec-bee9-0afd511c093b';
        $accessToken = env('MOVLY_TOKEN', false);

        $requestData = [
            'template_id' => $projectTemplateId,
            'options' => [
                'quality' => '480p',
                'create_render' => true,
                'create_project' => true
            ],
            'values' => $values
        ];

        $httpClient = new GuzzleClient();

        $response = $httpClient->post('https://api.moovly.com/generator/v1/jobs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json'
            ],
            'json' => $requestData
        ]);

        $jobs = json_decode($response->getBody()->getContents(), true);

        dd($jobs);
    }

    public function render()
    {
        $this->getTemplate();

        return view('livewire.test-api');
    }
}
