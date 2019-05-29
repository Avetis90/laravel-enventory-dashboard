<?php

namespace App\Services\Parsers;

use Exception;

class ParserFabric
{
    /**
     * options of converters
     */
    const OPTIONS = [
        'prefix' => '',
        'service_options' => '',
        'battery_packing' => '',
        'battery_type' => '',
        'description' => '',
        'description_cn' => ''
    ];
    /**
     * Parsers
     */
    const TYPE_ELIFE = 'elife';
    const TYPE_UBI_CA = 'ubiCa';
    const TYPE_UBI_AU = 'ubiAu';
    const TYPE_UBI_EU = 'ubiEu';
    const TYPE_UBI_NZ = 'ubiNz';
    const TYPE_UBI_US = 'ubiUs';
    const TYPE_UBI_ALL = 'ubiAll';
    const TYPE_WYT = 'wyt';
    const TYPE_INTERNAL = 'internal';
    const TYPE_MT = 'mt';
    const TYPE_PFC = 'pfc';
    const TYPE_OW_EU_DIRECT_LINE = 'owEuDirectLine';
    const TYPE_SONG = 'song';
    const TYPE_ALLINONE = 'Allinone';
    const TYPE_HUAXI = 'Huaxi';
    const TYPE_4PX = '4px';
    const TYPE_K5 = 'K5';
    /**
     * Create instance of parser
     * @param $type
     * @return mixed
     * @throws Exception
     */
    public static function create($type)
    {
        $class = 'App\\Services\\Parsers\\Parser' . ucfirst($type);
        if (class_exists($class)) {
            return new $class;
        } else {
            throw new Exception('Invalid parser class');
        }
    }

    /**
     * Return list of parsers
     * @return array
     */
    public static function parsers()
    {
        return [
            ParserFabric::TYPE_ELIFE => 'Elife',
            ParserFabric::TYPE_UBI_CA => 'UBI (CA)',
            ParserFabric::TYPE_UBI_AU => 'UBI (AU)',
            ParserFabric::TYPE_UBI_EU => 'UBI (EU)',
            ParserFabric::TYPE_UBI_NZ => 'UBI (NZ)',
            ParserFabric::TYPE_UBI_US => 'UBI (US)',
            ParserFabric::TYPE_UBI_ALL => 'UBI ALL',
            ParserFabric::TYPE_PFC => 'PFC',
            ParserFabric::TYPE_WYT => 'WYT',
            ParserFabric::TYPE_INTERNAL => 'Internal',
            ParserFabric::TYPE_ALLINONE => 'Allinone',
            ParserFabric::TYPE_4PX => '4px',
            ParserFabric::TYPE_K5 => 'K5',
            ParserFabric::TYPE_HUAXI => 'Huaxi',
            ParserFabric::TYPE_MT => 'MTrust',
            ParserFabric::TYPE_SONG => 'Song',
            ParserFabric::TYPE_OW_EU_DIRECT_LINE => 'OW EU Detect Line'
        ];
    }
}
