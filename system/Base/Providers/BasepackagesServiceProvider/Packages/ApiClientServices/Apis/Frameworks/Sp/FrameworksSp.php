<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks\Sp;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks\Frameworks;

class FrameworksSp extends Frameworks
{
    public function init($apiConfig = null, $api = null, $httpOptions = null, $monitorProgress = null)
    {
        if (!isset($apiConfig['category'])) {
            $apiConfig['category'] = 'Frameworks';
        }
        if (!isset($apiConfig['provider'])) {
            $apiConfig['provider'] = 'Sp';
        }

        parent::init($apiConfig, $api, $httpOptions, $monitorProgress);

        return $this;
    }

    public function registerOAuthClient($data)
    {
        if ($data['grant_type'] === 'authorization_code') {
            //Flow 1: Get Request to request_url to get state
            try {
                $state = $this->remoteWebContent->get($data['request_url'], [
                    'headers' => [
                        'Accept'        => 'application/json'
                    ]
                ]);
            } catch (\throwable $e) {
                trace([$e]);
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            if ($state->getStatusCode() === 200) {
                $stateResponse = json_decode($state->getBody()->getContents(), true);

                if (!isset($stateResponse['responseData']['authorization_url'])) {
                    trace(['meme']);
                    $this->addResponse('Did not receive valid authorization url from the server. Contact developer', 1);

                    return false;
                }
            } else {
                    trace(['meme']);
                $this->addResponse((string) $state->getBody(), 1);

                return false;
            }

            //Flow 2, make a GET call to $stateResponse['responseData']['authorization_url']
            try {
                $code = $this->remoteWebContent->get($stateResponse['responseData']['authorization_url'], [
                    'headers' => [
                        'Accept'        => 'application/json'
                    ]
                ]);
            } catch (\throwable $e) {
                trace([$e]);
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            if ($code->getStatusCode() === 200) {
                $codeResponse = json_decode($code->getBody()->getContents(), true);

                if (!isset($codeResponse['responseData']['registration_url']) &&
                    !isset($codeResponse['responseData']['code'])
                ) {
                    trace(['meme']);
                    $this->addResponse('Did not receive valid code from the server. Contact developer', 1);

                    return false;
                }
            } else {
                    trace(['meme']);
                $this->addResponse((string) $code->getBody(), 1);

                return false;
            }

            //Flow 3, make a POST call to $codeResponse['responseData']['registration_url']
            try {
                $code = $this->remoteWebContent->post($codeResponse['responseData']['registration_url'], [
                    'headers' => [
                        'Accept'        => 'application/json'
                    ],
                    'form_params'       => [
                        'client_id'         => $data['client_id'],
                        'client_secret'     => $data['client_secret'],
                        'grant_type'        => $data['grant_type'],
                        'redirect_uri'      => $data['redirect_uri'],
                        'code'              => $codeResponse['responseData']['code']
                    ]
                ]);
            } catch (\throwable $e) {
                trace([$e]);
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            if ($code->getStatusCode() === 200) {
                $tokenResponse = json_decode($code->getBody()->getContents(), true);

                if (!isset($tokenResponse['responseData']['access_token']) &&
                    !isset($tokenResponse['responseData']['refresh_token'])
                ) {
                    $this->addResponse('Did not receive valid access token from the server. Contact developer', 1);

                    return false;
                }
            } else {
                $this->addResponse((string) $code->getBody(), 1);

                return false;
            }

            if ($tokenResponse['responseData']['expires_in'] && $tokenResponse['responseData']['expires_in'] > 0) {
                $now = (\Carbon\Carbon::now('UTC'))->timestamp;

                $tokenResponse['responseData']['expires_in'] = $now + $tokenResponse['responseData']['expires_in'];
                $tokenResponse['responseData']['expires_in'] = \Carbon\Carbon::parse($tokenResponse['responseData']['expires_in'])->toAtomString();
            }

            $this->addResponse($tokenResponse['responseMessage'], $tokenResponse['responseCode'], $tokenResponse['responseData']);

            return true;
        } else if ($data['grant_type'] === 'client_credentials') {
            //
        }
    }

    public function getAvailableAPIGrantTypes()
    {
        return
            [
                'password'    =>
                    [
                        'id'            => 'password',
                        'name'          => 'Password Grant (With Refresh Token)',
                    ],
                'client_credentials'   =>
                    [
                        'id'            => 'client_credentials',
                        'name'          => 'Client Credential Grant'
                    ],
                'authorization_code'    =>
                    [
                        'id'            => 'authorization_code',
                        'name'          => 'Authorization Code Grant (With Refresh Token)',
                    ]
            ];
    }
}