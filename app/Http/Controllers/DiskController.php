<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiskController extends Controller
{
    public function index()
    {
        // Put in files/reno/


        Storage::put('test2.txt', 'test again');

        // If you didn't setup S3 as default disk in .env file
        $contents = Storage::disk('s3')->get('test2.txt');

        // get path
        $path = Storage::disk('s3')->path('test2.txt');

        var_dump( $path ); // This will show "test again text"
    }
}