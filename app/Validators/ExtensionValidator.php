<?php

namespace App\Validators;


use Illuminate\Validation\Validator;

class ExtensionValidator {


    public function extension($attribute, $value, $parameters, $validator) {
        return $value->getClientMimeType() == $parameters[0];
    }
}