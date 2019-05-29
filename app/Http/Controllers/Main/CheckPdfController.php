<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPdfChange;
use Illuminate\Http\Request;
use Gufy\PdfToHtml\Pdf;
use Gufy\PdfToHtml\Config;
use Illuminate\Support\Facades\Storage;

class CheckPdfController extends Controller {

    public function index() {
        return view('check-pdf.index');
    }

    public function change(CheckPdfChange $request) {
        Config::set('pdftohtml.bin', '/usr/bin/pdftohtml');
        Config::set('pdfinfo.bin', '/usr/bin/pdfinfo');
        $tempOutput = '/home/vagrant/htdocs/storage/app/files';
        Config::set('pdftohtml.output', $tempOutput);
        $temp = '/home/vagrant/htdocs/storage/app/files/current.pdf';

        //        $file = $request->file('file');
        //        $file->storeAs('files', 'current.pdf');

        $pdf = new Pdf($temp);
        var_dump($pdf->getInfo());
        $html = $pdf->html();
    }
}