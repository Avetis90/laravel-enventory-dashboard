<?php

namespace App\Services;

class ParserElifeHelper
{
    const MAX_AMOUNT_NUMBERS = 3;
    const ID_COMPANY = 51;
    const HEADERS = [
        'Company ID',
        'Order Date',
        'Bag No.',
        'Order ID',
        'Buyer ID',
        'Ship Full Name',
        'Ship Company',
        'Ship Address 1',
        'Ship Address 2',
        'Ship City',
        'Ship State/Province',
        'Ship Postal Code',
        'Ship Country',
        'Ship Phone',
        'Ship Email',
        'Requested Shipping',
        'Cost',
        'Item 1: SKU',
        'Item 1: Quantity',
        'Item 1: Unit Weight(LB)',
        'Item 1: Unit Price',
        'Item 1: Description',
        'Item 2: SKU',
        'Item 2: Quantity',
        'Item 2: Unit Weight(LB)',
        'Item 2: Unit Price',
        'Item 2: Description',
        'Item 3: SKU',
        'Item 3: Quantity',
        'Item 3: Unit Weight(LB)',
        'Item 3: Unit Price',
        'Item 3: Description',
        'Item 4: SKU',
        'Item 4: Quantity',
        'Item 4: Unit Weight(LB)',
        'Item 4: Unit Price',
        'Item 4: Description',
        'Item 5: SKU',
        'Item 5: Quantity',
        'Item 5: Unit Weight(LB)',
        'Item 5: Unit Price',
        'Item 5: Description',
    ];

    public static function getAutoIncrementColumns()
    {
        return [
            'Company ID',
            'Requested Shipping',
            'Cost',
            'Item 1: Unit Weight(LB)',
            'Item 1: Description',

            'Item 2: Unit Weight(LB)',
            'Item 2: Description',
            'Item 2: SKU',
            'Item 2: Quantity',
            'Item 2: Unit Price',

            'Item 3: Unit Weight(LB)',
            'Item 3: Description',
            'Item 3: SKU',
            'Item 3: Quantity',
            'Item 3: Unit Price',

            'Item 4: Unit Weight(LB)',
            'Item 4: Description',
            'Item 4: SKU',
            'Item 4: Quantity',
            'Item 4: Unit Price',

            'Item 5: Unit Weight(LB)',
            'Item 5: Description',
            'Item 5: SKU',
            'Item 5: Quantity',
            'Item 5: Unit Price',
            'Bag No.',
            'Buyer ID'
        ];
    }

    public static function getHeaders($line)
    {
        $rawHeaders = ParserElifeHelper::getRawHeaders();
        $headers = [];
        foreach ($line as $key => $val) {
            if (isset($rawHeaders[$val])) {
                $temp = $rawHeaders[$val];
                $temp['index'] = $key;
                $headers[$key] = $temp;
            }
        }
        return $headers;
    }

    public static function getBugNumber($itemDate, $currentItemNumber)
    {
        $data = new \DateTime($itemDate);
        $value = $data->format('md');
        $min = ParserElifeHelper::MAX_AMOUNT_NUMBERS - strlen($currentItemNumber);
        $item = '';
        for ($i = 1; $i <= $min; $i++) {
            $item .= '0';
        }
        $item .= $currentItemNumber;
        return ParserElifeHelper::ID_COMPANY . $value . $item;
    }

    public static function getRawHeaders()
    {
        return [
            'Company ID' => [
                'header' => 'Company ID',
                'defaultValue' => 'JF',
                'values' => []
            ],
            'Created at' => [
                'header' => 'Order Date',
                'index' => '',
                'values' => [],
            ],
            'Bag No.' => [
                'header' => 'Bag No.',
                'index' => '',
                'postFix' => 0,
                'preFix' => 51,
                'values' => []
            ],
            'Name' => [
                'header' => 'Order ID',
                'index' => '',
                'values' => [],
                'counter' => []
            ],
            'Buyer ID' => [
                'header' => 'Buyer ID',
                'defaultValue' => ''
            ],
            'Shipping Name' => [
                'header' => 'Ship Full Name',
                'index' => '',
                'values' => []
            ],
            'Shipping Company' => [
                'header' => 'Ship Company',
                'index' => '',
                'values' => []
            ],
            'Shipping Address1' => [
                'header' => 'Ship Address 1',
                'index' => '',
                'values' => []
            ],
            'Shipping Address2' => [
                'header' => 'Ship Address 2',
                'index' => '',
                'values' => []
            ],
            'Shipping City' => [
                'header' => 'Ship City',
                'index' => '',
                'values' => []
            ],
            'Shipping Province' => [
                'header' => 'Ship State/Province',
                'index' => '',
                'values' => []
            ],
            'Shipping Zip' => [
                'header' => 'Ship Postal Code',
                'index' => '',
                'values' => []
            ],
            'Shipping Country' => [
                'header' => 'Ship Country',
                'index' => '',
                'values' => []
            ],
            'Shipping Phone' => [
                'header' => 'Ship Phone',
                'index' => '',
                'values' => []
            ],
            'Email' => [
                'header' => 'Ship Email',
                'index' => '',
                'values' => []
            ],
            'Note' => [
                'header' => 'Notes',
                'index' => '',
                'values' => []
            ],
            'Requested Shipping' => [
                'header' => 'Requested Shipping',
                'defaultValue' => 'DHL GM',
                'values' => []
            ],
            'Cost' => [
                'header' => 'Cost',
                'defaultValue' => '',
                'values' => []
            ],
            'Lineitem sku' => [
                'header' => 'Item 1: SKU',
                'index' => '',
                'values' => []
            ],
            'Lineitem quantity' => [
                'header' => 'Item 1: Quantity',
                'index' => '',
                'values' => []
            ],
            'Item 1: Unit Weight(LB)' => [
                'header' => 'Item 1: Unit Weight(LB)',
                'defaultValue' => '',
                'values' => []
            ],
            'Lineitem price' => [
                'header' => 'Item 1: Unit Price',
                'index' => '',
                'values' => []
            ],
            'Item 1: Description' => [
                'header' => 'Item 1: Description',
                'defaultValue' => '',
                'values' => []
            ],
        ];
    }

    public static function setRightSort($item)
    {
        $newSortItem = [];
        foreach (ParserElifeHelper::HEADERS as $key => $value) {
            $newSortItem[] = $item[$value]['value'];
        }
        return $newSortItem;
    }
    public static function getCsvHeaders() {
        return ParserElifeHelper::HEADERS;
    }

}