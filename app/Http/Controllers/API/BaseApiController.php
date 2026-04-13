<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\PaginatesAndIncludes;

class BaseApiController extends Controller
{
    use PaginatesAndIncludes;
}
