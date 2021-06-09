<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusFileUpload extends Model
{
    use HasFactory;
    protected $table = 'status_file_upload';
    protected $fillable = [
        'name', 'status', 'type'
    ];
}
