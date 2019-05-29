<?php
/**
 * Created by PhpStorm.
 * User: DeVlas
 * Date: 01.09.2017
 * Time: 0:47
 */

namespace App\Services\Parsers;


class ParserSet extends AbstractShopifyParser
{
    public function parse($filePath, $options = [], $set = null)
    {
        $parsers = [];
        $options = ParserFabric::OPTIONS;
		$options['title'] = $set->title;
		$options['prefix'] = $set->prefix;
		$options['battery_packing'] = $set->battery_packing;
		$options['battery_type'] = $set->battery_type;
		$options['description'] = $set->description;
		$options['description_cn'] = $set->description_cn;
		
        foreach ($set->rules as $rule) {
            $converter = $rule->converter;
			/*
            foreach ($options as $key => $value) {
                $options[$key] = $converter->$key;
            }
			*/
		
			$options[$rule->country]['title'] = $rule->title;
			$options[$rule->country]['service_options'] = $rule->service_options;
            $parsers[$rule->country]['rule'] = $rule;
            $parsers[$rule->country]['converter'] = $converter;
            $parsers[$rule->country]['columns'] = ParserFabric::create($converter->converter_type)->getColumns
            ($options);
        }
		
        $this->SetBuildCsv($filePath, $parsers, $set->title);
    }
}