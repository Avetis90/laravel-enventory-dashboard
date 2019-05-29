<?php
/**
 * Created by PhpStorm.
 * User: DeVlas
 * Date: 01.09.2017
 * Time: 22:44
 */

namespace App\Models;

use App\Models\ConverterRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Set extends Model
{
    public function getRouteKey()
    {
        return $this->id;
    }
    protected $fillable = [
        'title',
        'prefix',
        'battery_packing',
        'battery_type',
        'description',
        'description_cn',
    ];

    public function rules() {
        return $this->hasMany(ConverterRule::class, 'set_id', 'id');
    }

    public function getDirPath() {
        $rootPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        return $rootPath . 'files/' . $this->id;
    }
}