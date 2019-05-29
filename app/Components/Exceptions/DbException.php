<?php

namespace App\Components\Exceptions;

use \Exception;

class DbException extends Exception
{
    const DEFAULT_MESSAGE = 'Server cannot apply changes to Data Base';

    function __construct($model){
        $message = DbException::DEFAULT_MESSAGE;
        parrent::__contruct($message, 500);
    }
}