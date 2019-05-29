<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Converter;
use Illuminate\Support\Facades\Storage;

class ConverterRule extends Model
{
    public function getRouteKey()
    {
        return $this->id;
    }
    protected $fillable = [
        'country',
        'converter_id',
        'service_options',
        'set_id'
    ];

    public function converter()
    {
        return $this->hasOne(Converter::class, 'id', 'converter_id');
    }

    public function hasFile() {
        $rootPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = 'files' . DIRECTORY_SEPARATOR .
            $this->set_id . DIRECTORY_SEPARATOR .
            $this->converter->converter_type . '.csv';
        return file_exists($rootPath . $path);
    }

    public function getPathToFile() {
        $rootPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = 'files' . DIRECTORY_SEPARATOR .
            $this->set_id . DIRECTORY_SEPARATOR .
            $this->converter->converter_type . '.csv';

        return $rootPath . $path;
    }
}