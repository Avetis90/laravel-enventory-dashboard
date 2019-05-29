<?php

namespace App\Services\Parsers;

use Countries;
use Closure;
use Exception;
use \ForceUTF8\Encoding;

abstract class AbstractShopifyParser extends AbstractParser
{
    /**
     * Build CSV file by rules
     * @param $filePath
     * @param $columns
     */
    public function buildCsv($filePath, $columns)
    {
        // Data for CSV building
        $csvContent = [];

        // Get raw data
        $data = $this->readCsvFile($filePath);
        $sourceHeaders = array_shift($data);

        // Process raw data
        $data = $this->groupItemsByOrder($data, $sourceHeaders);

        // Add columns to result
        $csvContent[] = array_keys($columns);

        // Add data
        foreach ($data as $items) {
            $row = [];
            // Process all needed values
            foreach ($columns as $options) {
                $row[] = $this->processColumn($items, $options, $sourceHeaders, $columns);
            }
            $csvContent[] = $row;
        }
        $this->writeCsvFile($csvContent);
    }

    public function setBuildCsv($filePath, $parsers, $setTitle)
    {
        // Data for CSV building
        $csvContent = [];

        // Get raw data
        $data = $this->readCsvFile($filePath);
        // First line (Headers of file)
        $sourceHeaders = $this->cleanUTF8Bom(array_shift($data));
        // Process raw data
        $data = $this->groupItemsByOrder($data, $sourceHeaders);
        // Add data
        $indexCountry = $this->getIndexByValue('Shipping Country', $sourceHeaders);
        $indexSKU = $this->getIndexByValue('Lineitem sku', $sourceHeaders);
        $indexOrderId = $this->getIndexByValue('Name', $sourceHeaders);
        //$qtyIndex = $this->getIndexByValue('Lineitem quantity', $sourceHeaders);
		
        //file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_tt.txt', print_r($parsers, true), FILE_APPEND);
        $countryRows = [];
        //$ubiCountries = array("AT", "GR", "PL", "SK", "LU", "HU", "HR", "EE", "SI");
        //$ubiCountries = array("MT-AT", "MT-PL", "MT-SK", "MT-LU", "MT-HU", "MT-HR", "MT-EE", "MT-GR", "MT-SI");
        $ubiCountries = array("MT-PL", "MT-SK", "MT-LU", "MT-HU", "MT-HR", "MT-EE", "MT-GR", "MT-SI");
		$battery_items = array("001-283", "001-28303", "001-28301", "001-28302", "001-28304", "001-282", "001-28201", "001-28202", "001-28205", "001-28204", "001-28203", "001-318", "001-321", "001-309", "001-30901", "001-30902", "001-30903", "001-30904", "001-30905", "001-370", "001-37001", "001-37002", "001-37003", "001-37004", "001-37005", "001-390", "001-335", "001-385");
		$orginal_sku_battery_items = array("sa_10kmah_solarcharger", "sa_y006solarcharger", "sa_12000_18650solarpowerbank", "Solar Power Bank 15000mAh with Electric Cigarette", "sa_3fold_solarcharger", "sa_4fold_solarcharger", "sa_1800window_solar_powerbank", "sa_cpb400_jump_starter_powerbank", "sa_mfc_camp_light_bs3500");
		$have_battery_countries = array("AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GB", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE");
		
		$ctsItems = array("ML251751", "ML251752", "ML251753", "ML251951", "ML251952", "ML25400-1");
		$ctsMethod = array("BE" => "DHLEU1", "MC" => "DHLEU1", "PL" => "DHLEU1", "SE" => "DHLEU1", "CY" => "DHLEU1", "MT" => "DHLEU1", "PT" => "DHLEU1", "GB" => "DHLEU1", "ES" => "DHLEU1", "RO" => "DHLEU1", "AT" => "DHLEU2", "CZ" => "DHLEU2", "LU" => "DHLEU2", "HU" => "DHLEU2", "SK" => "DHLEU2", "SI" => "DHLEU2", "BG" => "DHLEU2", "HR" => "DHLEU2", "EE" => "DHLEU2", "FI" => "DHLEU2", "IE" => "DHLEU2", "LV" => "DHLEU2", "LT" => "DHLEU2", "DK" => "DHLEU3", "FR" => "DHLEU3", "NL" => "DHLEU3", "IT" => "DHLEU3", "GR" => "DHLEU3", "DE" => "DHL_L_SMAL", "DHL-DE" => "DHL_L_SMAL");
		$ctsCountries = array("AT", "CZ", "DE", "SK", "HU", "BG", "FI", "IE", "LT", "DHL-DE");
		
		$fourPxItems = array("ML251953", "ML251751", "ML251752", "ML251753", "ML25400-1");
		$fourPxCountries = array("US", "GU", "DHL-US");
		
        foreach ($data as $items) {
            $itemCountry = $this->getCountryItems($items, $indexCountry, array_keys($parsers));//isset
            
            //** added to convert over qty to Mtrust
            $itemSkus = $items[0][$indexSKU];
            $itemQty = intval(rtrim(stringAfter("(", trim($items[0][$indexSKU])), ")"));
			
            
            //if (($items[0][$indexCountry] == "AT") && ((stripos($itemSkus, ", ")!==FALSE) || $itemQty > 1) && stripos($setTitle, "ModernistLook") !== FALSE){
			// Auto copy UBI orders to MT for overweight item
			$allSku = "";
			foreach ($items as $item){
				$allSku .= $item[$indexSKU] . ", ";
			}
			
			//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_line.txt', $items[0][$indexOrderId] . " - " . print_r($allSku, true), FILE_APPEND);
			if ($items[0][$indexCountry] == "GR"){
				$itemSkus = str_replace(", ", ",", $itemSkus);
			}

			//Find battery items & convert it to Internal file
			if ((strposa($allSku, $orginal_sku_battery_items) !== FALSE) && (stripos($setTitle, "StealthAngel") !== FALSE) && (!in_array($items[0][$indexCountry], $have_battery_countries))){
                $itemCountry = "INT";
				
			// Find multiple items for backpack
			} elseif (in_array("MT-" . $items[0][$indexCountry], $ubiCountries) && ((stripos($itemSkus, ", ")!==FALSE) || $itemQty > 1) && stripos($setTitle, "WooSP") !== FALSE) {
                $itemCountry = "MT-" . $items[0][$indexCountry];
				if (!isset($parsers[$itemCountry])) {
					$countryArrayObject = new \ArrayObject($parsers['ZZ']);
					$parsers[$itemCountry] = $countryArrayObject->getArrayCopy();
				}
            }
			
			if (stripos($setTitle, "WooSP") !== FALSE){
				if(isset($parsers['CTS']) && in_array($itemCountry, $ctsCountries) && checkCTSitems($itemSkus, $ctsItems)){
					$oldItemCountry = $items[0][$indexCountry];
					$itemCountry = "CTS-" . $items[0][$indexCountry];
					if (!isset($parsers[$itemCountry])) {
						$countryArrayObject = new \ArrayObject($parsers['CTS-' . $ctsMethod[$oldItemCountry]]);
						$parsers[$itemCountry] = $countryArrayObject->getArrayCopy();
					}
				}
				
				if(isset($parsers['4px']) && in_array($itemCountry, $fourPxCountries) && checkCTSitems($itemSkus, $fourPxItems)){
					$oldItemCountry = $items[0][$indexCountry];
					$itemCountry = "4px-US";
					if (!isset($parsers[$itemCountry])) {
						$countryArrayObject = new \ArrayObject($parsers['4px']);
						$parsers[$itemCountry] = $countryArrayObject->getArrayCopy();
					}
				}
			}
            
            $countryRows[$itemCountry][] = $items;
        }
		
        $csvContent = [];
        $rules = [];
		$rowSkus = "";
		$rowQty = "";
		//$mtCountries = array("AT", "GR", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "DE", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "SE");
		//$mtCountries = array("AT", "GR", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "DE", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "SE");
		$mtCountries = array();
		//$multi_countryToConverter = array("NO" => "Allinone", "CH" => "Allinone");
		$multi_countryToConverter = array();
		
		//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_line.txt', print_r($parsers, true), FILE_APPEND);
		//ksort($countryRows);
		
        foreach ($countryRows as $country => $itemsContainer) {
            if (isset($parsers[$country])) {
                $columns = $parsers[$country]['columns'];
                $rules[$parsers[$country]['converter']->converter_type]['rule'] = $parsers[$country]['rule'];
				
				if (stripos($setTitle, "WooSP") !== FALSE){
					$rules[$parsers['CTS']['converter']->converter_type]['rule'] = $parsers['CTS']['rule'];
					$rules[$parsers['4px']['converter']->converter_type]['rule'] = $parsers['4px']['rule'];
				}
                //set headers
                if (!isset($csvContent[$parsers[$country]['converter']->converter_type])) {
                    $csvContent[$parsers[$country]['converter']->converter_type][] = array_keys($columns);
                }
				
				//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_columns.txt', print_r($columns, true), FILE_APPEND);
				//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_line.txt', print_r($itemsContainer, true), FILE_APPEND);
                foreach ($itemsContainer as $row) {
                    // Process all needed values
                    $line = [];
                    
					//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_line.txt', print_r($line, true), FILE_APPEND);
					$rowSkus = $row[0][$indexSKU];
					$rowQty = intval(rtrim(stringAfter("(", trim($row[0][$indexSKU])), ")"));
                    
                    foreach ($columns as $options) {
                        $line[] = $this->processColumn($row, $options, $sourceHeaders, $columns);
                    }
					
					//Find orders with more than 2 items and move them to Mtrust
					if (((stripos($rowSkus, ", ")!==FALSE) || $rowQty > 1) && stripos($setTitle, "WooSP") !== FALSE && (stripos($rowSkus, "ML25400") === FALSE) && (in_array($country, $mtCountries) || in_array($country, $ubiCountries) || array_key_exists($country, $multi_countryToConverter))){
						if (in_array($country, $mtCountries) || in_array($country, $ubiCountries))
						{
                            $lineArrayObject = new \ArrayObject($line);
                            $newLine = $lineArrayObject->getArrayCopy();
                            $newLine[2] = '淼信欧洲通专线';
                            
                            if ($country == "MT-AT"){
                                $newLine[9] = 'AT';
                            }
                            
							$csvContent['Allinone'][] = $newLine;
                            
						} elseif (array_key_exists($country, $multi_countryToConverter)) {
							$alphabet = range('A', 'Z');
							
							if (stripos($rowSkus, ", ")!==FALSE) {
								$newSkus = explode(",", $rowSkus);
								
								for ($a = 1; $a <= count($newSkus); $a++){
									$lineArrayObject = new \ArrayObject($line);
									$newLine = $lineArrayObject->getArrayCopy();
									
									if ($a > 1){
										$newLine[$indexOrderId] = $line[$indexOrderId] . $alphabet[$a];
									}
									
									//$newLine[$indexSKU] = trim($newSkus[$a-1]);
									$newLine[13] = trim($newSkus[$a-1]);
									$csvContent[$multi_countryToConverter[$country]][] = $newLine;
									
									unset($newLine);
								}
								
							} else {
								for ($i = 1; $i <= $rowQty; $i++){
									$lineArrayObject = new \ArrayObject($line);
									$newLine = $lineArrayObject->getArrayCopy();
									
									if ($i > 1){
										$newLine[$indexOrderId] = $line[$indexOrderId] . $alphabet[$i];
									}
									
									$newLine[13] = str_replace("(" . $rowQty . ")", "(1)", $rowSkus);
									//$newLine[$indexSKU] = "(" . $rowQty . ")";
									//$newLine[$indexSKU] = print_r($newLine, true);
									$csvContent[$multi_countryToConverter[$country]][] = $newLine;
									
									unset($newLine);
								}
							}
						} else {
							//if (isset($parsers['CTS']) && checkCTSitems($rowSkus, $ctsItems)){
							//	$csvContent[$parsers['CTS-' . $ctsMethod[$country]]['converter']->converter_type][] = $line;
							//} else {
								$csvContent[$parsers[$country]['converter']->converter_type][] = $line;
							//}
                        }
					} else {
							//if (isset($parsers['CTS']) && checkCTSitems($rowSkus, $ctsItems)){
							//if (isset($parsers['CTS']) && in_array($country, $ctsCountries) && checkCTSitems($rowSkus, $ctsItems)){
							//	$csvContent[$parsers['CTS-' . $ctsMethod[$country]]['converter']->converter_type][] = $line;
							//}elseif (isset($parsers['4px']) && in_array($country, $fourPxCountries) && checkCTSitems($rowSkus, $fourPxItems)){
							//	$csvContent[$parsers['4px']['converter']->converter_type][] = $line;
							//} else {
								$csvContent[$parsers[$country]['converter']->converter_type][] = $line;
							//}
					}
                }
            } else {
                throw new Exception("Country({$country}) for parser not set");
            }
        }
		
		
		//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_csv.txt', print_r($csvContent, true), FILE_APPEND);
        //if (stripos($setTitle, "Woo") !== FALSE){
            // Sort Song's 单 by SKU
            // 'song' => array(22, 4),

        $sort_orders = array('song' => array(3, 13, 2), 'Allinone' => array(3, 13, 2), '4px' => array(3, 13, 2), 'K5' => array(3, 13, 2), 'Huaxi' => array(3, 13, 2), 'mt' => array(3, 13, 2), 'internal' => array(13, 3, 2), 'wyt' => array(13, 3, 2), 'ubiAll' => array(24, 11), 'owEuDirectLine' => array(36, 10), 'pfc' => array(35, 9), 'elife' => array(0, 0));
        
        foreach ($csvContent as $converter => $content){
            $new_SongCSVContent = '';
            $song_headers = array();
            $sku_items = array();
            $country_items = array();
			$ship_methods = array();
            
            $song_headers[] = $csvContent[$converter][0];
            $song_csvContent = array_splice($csvContent[$converter], 1);
            foreach ($song_csvContent as $key => $row) {
                $sku_items[$key]  = $row[$sort_orders[$converter][0]];
                $country_items[$key]  = $row[$sort_orders[$converter][1]];
				
				if (in_array($converter, array("song", 'Allinone', '4px', 'K5', 'Huaxi', "mt", "wyt", "internal"))){
					$ship_methods[$key]  = $row[$sort_orders[$converter][2]];
				}
            }
            
			if (in_array($converter, array("song", 'Allinone', '4px', 'K5', 'Huaxi', "mt", "wyt", "internal"))){
				array_multisort($ship_methods, SORT_ASC, $country_items, SORT_ASC, $sku_items, SORT_ASC, $song_csvContent);
			} else {
				array_multisort($sku_items, SORT_ASC, $country_items, SORT_ASC, $song_csvContent);
			}
            
            $new_SongCSVContent = array_merge($song_headers, $song_csvContent);
            unset($csvContent[$converter]);
            $csvContent[$converter] = $new_SongCSVContent;
        }
        //}
        
        //file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_tt.txt', print_r($csvContent, true), FILE_APPEND);
        
        $this->writeSetCsvFiles($csvContent, $rules);
    }

    /**
     * Group all items by order number
     * @param array $data
     * @param array $sourceHeaders
     * @return array
     */
    public function groupItemsByOrder(array $data, array $sourceHeaders)
    {
        $orders = [];
		$uniqueProducts = array();
		$previousOrderId = '';
		$duplicates = "";
		
        $orderNumberIndex = array_search('Name', $sourceHeaders);
        foreach ($data as $line) {
            if (!isset($orders[$line[$orderNumberIndex]])) {
                $orders[$line[$orderNumberIndex]] = [];
            }
            
			if (!array_key_exists($line[$orderNumberIndex], $uniqueProducts)){
				$orders[$line[$orderNumberIndex]][] = $line;
			}
			
			if (!empty($previousOrderId) && $line[$orderNumberIndex] != $previousOrderId){
				$uniqueProducts[$previousOrderId] = 1;
			}
            $previousOrderId = $line[$orderNumberIndex];
        }
        return $orders;
    }

    /**
     *
     * @param $items - Items in each order
     * @param $options - Options for each column
     * @param $sourceHeaders - Source header values
     * @param $columns - List of all columns
     * @return string
     */
    public function processColumn($items, $options, $sourceHeaders, $columns)
    {
        $value = '';
        if (isset($options['default'])) {
            $value = $options['default'];
        } elseif (isset($options['attribute'])) {
            $index = array_search($options['attribute'], $sourceHeaders);
            $line = reset($items);
            if ($index !== false && isset($line[$index]) && !empty($line)) {
                $value = $line[$index];
            }
        } elseif (isset($options['callback']) && $options['callback'] instanceof Closure) {
            $value = $options['callback']($items, $sourceHeaders);
        } elseif (isset($options['alias'])) {
            if (isset($columns[$options['alias']])) {
                $value = $this->processColumn($items, $columns[$options['alias']], $sourceHeaders, $columns);
            }
        }
        if (isset($options['prefix'])) {
            $value = $options['prefix'] . $value;
        }
        if (isset($options['postfix'])) {
            $value .= $options['postfix'];
        }
        return $value;
    }

    /**
     * @param $value - needle in header
     * @param $sourceHeaders - headers of file
     * @return mixed
     */
    public function getIndexByValue($value, $sourceHeaders)
    {
        return array_search($value, $sourceHeaders);
    }

    /**
     * Method return country, if country not fount thtow exception.
     * @param $items
     * @param $countryIndex
     * @param $counties
     * @return string
     * @throws Exception
     */
    public function getCountryItems($items, $countryIndex, $counties)
    {
        $country = null;
		$search_replace_countries = array("South Korea" => "Korea, Republic of", "Brunei" => "Brunei Darussalam", "Taiwan" => "Taiwan, Province of China", "Russia" => "Russian Federation");

        foreach ($items as $item) {
            if (!empty($item[$countryIndex])) {
				$country = $item[$countryIndex];
				
				if (strlen($country) > 2 && stripos($country, "-")===FALSE){
                    if (array_key_exists($country, $search_replace_countries)){
                        $country = $search_replace_countries[$country];
                    }
					
					try {
						$country_info = Countries::where('name', $country)->first();
						$country_ab = $country_info->iso_3166_2;
					} catch(Exception $e) {
						throw new Exception($country . " is causing error. Might not exist in database.");
					}
                    
				} else {
					$country_ab = $country;
					break;
				}
            }
        }
        if(empty($country_ab)) {
            throw new Exception("One of order does not have any country");
        }
        return $country_ab;
    }
    
    /**
     * @param $sourceHeaders - headers of file
     * @return array
     */
    public function cleanUTF8Bom($sourceHeaders)
    {
		$newSourceHeaders = array();
		foreach ($sourceHeaders as $item){
			$bom = pack('H*','EFBBBF');
			$newSourceHeaders[] = preg_replace("/^$bom/", '', $item);
		}
		return $newSourceHeaders;
    }
}
