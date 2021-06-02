<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Service 
{
    private $PATH;
    private $TOKEN;
    private $HEADER;

    public function __construct()
    {
        $this->PATH = env('BASE_PATH');
        $this->TOKEN = env('TOKEN');
    $this->HEADER = ['Authorization'=>"Bearer $this->TOKEN"/*, "Content-Type"=>"application/xml"*/];
    }

    public function getHttp($endpoint)
    {
        try {
            return Http::withHeaders($this->HEADER)->get($this->PATH.$endpoint);
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());
        }
    }

    public function putHttp($endpoint, $data)
    {
        try {
            return Http::withHeaders($this->HEADER)->put($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());
        }
    }

    public function postHttp($endpoint, $data)
    {
        try {            
            return Http::withHeaders($this->HEADER)->post($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());
        }
    }
    
    public function postFileHttp($endpoint, $file, $type)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $this->PATH . $endpoint, [
            'headers' => $this->HEADER,
            'multipart' => [
                [
                    'name'     => $type,
                    'contents' => file_get_contents($file),
                    'filename' => $file
                ]
            ],
        ]);
        if($type =='invoice'){
            return $res->getStatusCode();
        }
        return $res->getBody();
    }
}