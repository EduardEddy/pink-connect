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
        $this->HEADER = ['Authorization'=>"Bearer $this->TOKEN"];
    }

    public function getHttp($endpoint)
    {
        try {
            return Http::withHeaders($this->HEADER)->get($this->PATH.$endpoint);
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());
        }
    }
}