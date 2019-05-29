<?php

namespace App\Services\Parsers;

use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractParser implements ParserInterface
{
    /**
     * Path to source file
     * @var
     */
    public $sourceFilePath;

    /**
     * Path to target file
     * @var
     */
    public $targetFilePath;

    /**
     * Read VSC file file line by line
     * @param $filePath
     * @return array
     * @throws Exception
     */
    public function readCsvFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }
        $this->sourceFilePath = $filePath;

        $data = [];

        if (($handle = $this->fopen_utf8($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }

        if (count($data) < 2) {
            throw new Exception('Invalid CSV file');
        }

        return $data;
    }

    /**
     * Write data as CSV to output
     * @param array $data
     * @param string $filePath
     */
    public function writeCsvFile(array $data, $filePath = null)
    {
        if ($filePath === null) {
            $filePath = tempnam(sys_get_temp_dir(), 'csv');
        }
        $this->targetFilePath = $filePath;

        $stream = fopen($filePath, 'w');
		fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($data as $row) {
            fputcsv($stream, $row);
        }
        fclose($stream);
    }

    public function writeSetCsvFiles(array $data, $rules)
    {
        $rootPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        foreach ($data as $converter => $rows) {
            $rule = $rules[$converter]['rule'];
            $path = 'files' . DIRECTORY_SEPARATOR .
                $rule->set_id . DIRECTORY_SEPARATOR .
                $converter . '.csv';
            Storage::put($path, '');

            $fullPath = $rootPath . $path;
            $stream = fopen($fullPath, 'w');
			fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $item) {
                fputcsv($stream, $item);
            }
            fclose($stream);
        }
    }
    
    public function fopen_utf8($filename){
        $encoding='';
        $handle = fopen($filename, 'r');
        $bom = fread($handle, 2);
        //	fclose($handle);
        rewind($handle);
        
        if($bom === chr(0xff).chr(0xfe)  || $bom === chr(0xfe).chr(0xff)){
			// UTF16 Byte Order Mark present
			$encoding = 'UTF-16';
        } else {
            $file_sample = fread($handle, 1000); //read first 1000 bytes
            // + e is a workaround for mb_string bug
            rewind($handle);
            
            $encoding = mb_detect_encoding($file_sample , 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP');
        }
        if ($encoding){
            stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8');
        }
        return  ($handle);
    } 

    /**
     * Clean temp files
     */
    public function clean()
    {
        foreach ([
                     $this->sourceFilePath,
                     $this->targetFilePath
                 ] as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
