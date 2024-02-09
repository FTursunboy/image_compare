<?php

namespace App\Http\Controllers;

use App\Jobs\JobCommand;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class ApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        JobCommand::dispatch();
    }


}

