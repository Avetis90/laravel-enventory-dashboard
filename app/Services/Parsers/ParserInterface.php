<?php

namespace App\Services\Parsers;

use App\Models\Set;

interface ParserInterface
{
    /**
     * Process file content
     * @param $filePath
     */
    public function parse($filePath, $options = [], $set);
}
