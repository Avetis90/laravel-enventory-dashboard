<?php
/**
 * Created by PhpStorm.
 * User: DeVlas
 * Date: 05.08.2017
 * Time: 11:58
 */

namespace App\Http\Controllers\Main;
use App\Http\Controllers\Controller;

class ServiceConstroller extends Controller{

    public function notFound() {
        return view('service.not_found');
    }
}