<?php

namespace App\Services;

use Google;
use Google\Service\TrafficDirectorService\GoogleRE2;

Class GoogleService
{
    public $google;

    protected $scope;

    protected $redirect;

    public function __construct($redirect=null, $scope=null)
    {
        if (null !== $scope) $this->scope = $scope;
        if (null !== $redirect) $this->redirect = $redirect;

        $this->google = new Google\Client;
        $this->google->setClientId(config('google.oauth_client_id'));
        $this->google->setClientSecret(config('google.oauth_client_secret'));
        $this->google->setScopes($this->scope ?? [
            Google\Service\YouTube::YOUTUBE_READONLY,
            Google\Service\YouTube::YOUTUBE_UPLOAD
        ]);
        $this->google->setRedirectUri($this->redirect);

        if(session('youtube-token')) {
            $this->google->setAccessToken(session('youtube-token')['access_token']);
        }

    }

    public function auth_url()
    {
        return $this->google->createAuthUrl();
    }

    public function fetchAccessToken($code)
    {
        return $this->google->fetchAccessTokenWithAuthCode($code);
    }

    public function getVideoCategories()
    {
        $youtube = new Google\Service\YouTube($this->google);

        $queryParams = [
            'regionCode' => 'ID'
        ];

        return $youtube->videoCategories->listVideoCategories('snippet', $queryParams);
    }

    public function youtube()
    {
        return new Google\Service\YouTube($this->google);
    }
}
