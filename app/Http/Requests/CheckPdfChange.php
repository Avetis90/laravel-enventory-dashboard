<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckPdfChange extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'bail|extension:application/pdf,pdf|required|file',
            'pattern'=> 'bail|required|string',
            'size' => 'bail|required|numeric'
        ];
    }
    public function messages() {
        return [
          'pattern.required' => 'Can not be Empty',
          'size.required' => 'Can not be Empty',
        ];
    }
}
