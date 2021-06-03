<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service 
{
    private $PATH;
    private $TOKEN;
    private $HEADER;

    public function __construct()
    {
        $this->PATH = env('BASE_PATH');
        $this->TOKEN = env('TOKEN');
        $this->HEADER = ['Authorization'=>"Bearer $this->TOKEN"];
    }

    public function getHttp($endpoint)
    {
        try {
            return Http::withHeaders($this->HEADER)->get($this->PATH.$endpoint);
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }

    public function putHttp($endpoint, $data)
    {
        try {
            return Http::withHeaders($this->HEADER)->put($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }

    public function putWithHeaders($header,$endpoint, $data)
    {
        try {
            return Http::withHeaders($header)->put($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }


    public function postHttp($endpoint, $data)
    {
        try {            
            return Http::withHeaders($this->HEADER)->post($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }

    public function postWithHeaders($header,$endpoint, $data)
    {
        try {
            return Http::withHeaders($header)->post($this->PATH.$endpoint, [$data]);
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }
}