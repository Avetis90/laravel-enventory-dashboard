<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\Parsers\ParserFabric;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Converter;

class ConverterCsvController extends Controller
{
    public function index()
    {
        return view(
            'converter-csv.index',
            [
                'parsers' => ParserFabric::parsers(),
            ]
        );
    }

    public function change(request $request)
    {
        $this->validate($request, [
            'parser' => [
                'bail',
                'required',
                Rule::in(array_keys(ParserFabric::parsers())),
            ],
            'file' => [
                'bail',
                'required',
            ],
        ]);
        $options = ParserFabric::OPTIONS;
        $converter = Converter::where(['converter_type' => $request->input('parser')])->first();
        if ($converter) {
            foreach ($options as $key => $value) {
                if (empty($request->input($key)) && isset($converter->$key)) {
                    $options[$key] = $converter->$key;
                } else {
                    $options[$key] = $request->input($key);
                }
            }
        } else {
            $options = array_replace($options, $request->all());
        }

        $fileName = $request->input('parser') . '.csv';
        $request->file('file')->storeAs('files', $fileName);
        $parser = ParserFabric::create($request->input('parser'));

        $pathToFile = storage_path('app/files/' . $fileName);
        $parser->parse($pathToFile, $options);
        $content = file_get_contents($parser->targetFilePath);
        $parser->clean();

        $outputFileName = $request->input('parser') . '-' . date('siHmY') . '.csv';
        return response($content)->withHeaders([
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $outputFileName,
        ]);
    }

    public function converters(Request $request) {
        return view(
            'converter-csv.converters',
            [
                'converters' => Converter::all(),
                'prettyNames' => ParserFabric::parsers()
            ]
        );
    }

    public function update(Converter $model, Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'converter_type' => [
                    'bail',
                    'required',
                    Rule::in(array_keys(ParserFabric::parsers())),
                    Rule::unique('converters')->ignore($model->id, 'id')
                ],
            ]);
            $model->fill($request->all());
            if (!$model->save()) {
                return redirect('converters')->with('error', 'Converter update has failed');
            }
            return redirect('converters')->with('success', 'Record successfully updated');
        }
        return view('converter-csv.update', [
            'model' => $model,
            'parsers' => ParserFabric::parsers(),
        ]);
    }

    public function create(Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'converter_type' => [
                    'bail',
                    'required',
                    Rule::in(array_keys(ParserFabric::parsers())),
                    'unique:converters,converter_type'
                ],

            ]);
            if (!Converter::create($request->all())) {
                return redirect('converters')->with('error', 'Record not saved');
            }
            return redirect('converters')->with('success', 'Record successfully created');
        }
        return view('converter-csv.create', [
            'parsers' => ParserFabric::parsers(),
        ]);
    }

    public function delete(Converter $model) {
        if (!$model->delete()) {
            return redirect('converters')->with('error', 'Converter deleted has failed');
        };
        return redirect('converters')->with('success', 'Converter successfully deleted');
    }
}
