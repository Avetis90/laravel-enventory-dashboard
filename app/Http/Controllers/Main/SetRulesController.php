<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use App\Models\Converter;
use App\Models\ConverterRule;
use App\Models\Set;

use App\Services\Parsers\ParserFabric;
use App\Services\Parsers\ParserSet;
use DateTime;
use DateTimeZone;
use Excel;
use PHPExcel_Cell_MyValueBinder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use \Exception;

class SetRulesController extends Controller
{

    public function index()
    {
        $sets = Set::with('rules.converter')->get();
		$setFiles = [];
		
		foreach ($sets as $set){
			if (!isset($setFiles[$set->id])){
				$setFiles[$set->id] = [];
			}
			foreach ($set->rules as $rule){
				if (!in_array($rule->converter->converter_type, $setFiles[$set->id]))
				{
					$setFiles[$set->id][] = $rule->converter->converter_type;
					
					if (file_exists($rule->getPathToFile())){
						$tz = 'Asia/Hong_Kong';
						$timestamp = time();
						$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
						$dt->setTimestamp(filemtime($rule->getPathToFile())); //adjust the object to correct timestamp
						
						$fileDates[$set->id][] = $dt->format('m-d, H:i');
					} else {
						$fileDates[$set->id][] = "";
					}
				}
			}
		}
		
        return view('sets-rules.index', [
            'sets' => $sets,
            'setFiles' => $setFiles,
            'fileDates' => $fileDates,
            'prettyNames' => ParserFabric::parsers()
        ]);
    }

    public function create(Request $request)
    {
        $converters = Converter::all();
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'title' => [
                    'bail',
                    'string',
                    'required',
                ],
            ], [
                'converter_id.required' => 'Can not be empty'
            ]);
            if (!Set::create($request->all())) {
                return redirect('sets-rules')->with('error', 'Record not saved');

            }
            return redirect('sets-rules')->with('success', 'Record successfully created');
        }
        return view('sets-rules.create', [
            'converters' => $converters,
            'prettyNames' => ParserFabric::parsers()
        ]);
    }

    public function update(Set $model, Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'title' => [
                    'bail',
                    'required',
                    'string'
                ]
            ], [
                'converter_id.required' => 'Can not be empty'
            ]);
            $model->fill($request->all());
            if (!$model->save()) {
                return redirect('sets-rules')->with('error', 'Set has failed');
            }
            return redirect('sets-rules')->with('success', 'Set successfully update');
        }
        return view('sets-rules.update', [
            'model' => $model
        ]);
    }

    public function delete(Set $model)
    {

        DB::beginTransaction();
        try {
            $rules = $model->rules;
            foreach($rules as $rule) {
                $pathToFile = $rule->getPathToFile();
                if (file_exists($pathToFile)) {
                    unlink($pathToFile);
                }
                if (!$rule->delete()) {
                    throw new Exception('Rule not deleted');
                }
            }

            if(!rmdir($model->getDirPath())) {
                throw new Exception("Files of set does not deleted");
            }
            if (!$model->delete()) {
                throw new Exception("Set not deleted");
            };

            DB::commit();
            return redirect('sets-rules')->with('success', 'Set of rules successfully deleted');
        } catch(Exception $e) {
            DB::rollback();
            return redirect('sets-rules')->with('error', 'Set of rules deleted has failed');

        }

    }

    public function rules(Request $request, Set $model)
    {
        return view('sets-rules.rules', [
            'set' => $model,
            'prettyNames' => ParserFabric::parsers(),
        ]);
    }

    public function ruleUpdate(Request $request, Set $set, ConverterRule $rule) {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'converter_id' => [
                    'bail',
                    'required',
                    Rule::exists('converters', 'id'),
                ],
                'country' => [
                    'bail',
                    'required'
                ],
            ], [
                'converter_id.required' => 'Can not be empty'
            ]);
            $rule->fill($request->all());
            if (!$rule->save()) {
                return redirect()->route('rules-set', ['id' => $set->id])->with('error', 'Update advanced converter rule has failed');
            }
            return redirect()->route('rules-set', ['id' => $set->id])->with('success', 'Record successfully update');
        }
        $converters = Converter::all();
        return view('sets-rules.rule-update', [
            'set' => $set,
            'model' => $rule,
            'converters' => $converters,
            'prettyNames' => ParserFabric::parsers(),
        ]);
    }

    public function ruleCreate(Request $request, Set $model)
    {
        $converters = Converter::all();
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'converter_id' => [
                    'bail',
                    'required',
                    Rule::exists('converters', 'id'),
                ],
                'country' => [
                    'bail',
                    'required',
                    Rule::unique('converter_rules', 'country')->where(function($query) use ($model) {
                        $query->where('set_id', $model->id);
                    })
                ],

            ], [
                'converter_id.required' => 'Can not be empty'
            ]);
            $params = array_merge($request->all(), ['set_id' => $model->id]);
            if (!ConverterRule::create($params)) {
                return redirect()->route('rules-set', ['id' =>$model->id])->with('error', 'Record not saved');

            }
            return redirect()->route('rules-set', ['id' => $model->id])->with('success', 'Record successfully created');
        }
        return view('sets-rules.rule-create', [
            'set' => $model,
            'converters' => $converters,
            'prettyNames' => ParserFabric::parsers()
        ]);
    }

    public function ruleDelete(Request $request, Set $set, ConverterRule $rule) {
       if (!$rule->delete()) {
           return redirect()->route('rules-set', ['id' =>$set->id])->with('error', 'Rule deleted has failed');
       }
        return redirect()->route('rules-set', ['id' =>$set->id])->with('success', 'Rule successfully deleted');
    }

    public function change(Request $request)
    {
        $this->validate($request, [
            'file' => [
                'bail',
                'required',
            ],
            'set' => [
                'bail',
                'required',
                Rule::exists('sets', 'id'),
            ]
        ]);
        //try {
            $set = Set::find($request->input('set'));
            if (is_null($set)) {
                throw new Exception('Set of rules does not exist');
            }

            $fileName = $set->title . '-rules.csv';
            $request->file('file')->storeAs('files', $fileName);
            $pathToFile = storage_path('app/files/' . $fileName);

            $parser = new ParserSet();
            $parser->parse($pathToFile, [], $set);
            $parser->clean();
            return redirect('sets-rules')->with('success', 'Record convereted');
        //} catch(Exception $e) {
        //    return redirect()->route('sets-rules')->with('error', $e->getMessage());
        //}

    }

    public function downloadFile(Request $request, $rule) {
        $path = $rule->getPathToFile();
        $content = file_get_contents($path);
        $prettyNames = ParserFabric::parsers();
        $fileName = $prettyNames[$rule->converter->converter_type] . " - " . date('Y-m-d'). '.csv';
        return response($content)->withHeaders([
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename=' .$fileName,
        ]);
    }

    public function downloadRuleFile(Request $request, $set_id, $converter_type) {		
		$convert_type = (stripos($converter_type, "ubi") !== false) ? "csv" : "xls";
		
        $rootPath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
		$prettyNames = ParserFabric::parsers();
        $path = $rootPath . 'files' . DIRECTORY_SEPARATOR .
            $set_id . DIRECTORY_SEPARATOR .
            $converter_type . '.csv';
			
		if ($convert_type == "csv"){
			$content = file_get_contents($path);
			$fileName = $prettyNames[$converter_type] . " - " . date('Y-m-d'). '.csv';
			
			return response($content)->withHeaders([
				'Content-Type' => 'text/csv',
				'Content-Transfer-Encoding' => 'UTF-8',
				'Content-Disposition' => 'attachment; filename=' .$fileName,
			]);
		} else {
			Excel::setValueBinder( new PHPExcel_Cell_MyValueBinder() );
            Excel::load($path)->download('xls', array('Content-Disposition' => 'attachment; filename=' . $prettyNames[$converter_type] . " - " . date('Y-m-d'). '.xls'));
            /*
            Excel::load($path)->download('xls', array('Content-Disposition' => 'attachment; filename=' . $prettyNames[$converter_type] . " - " . date('Y-m-d'). '.xls')) implements PHPExcel_Cell_IValueBinder 
				{ 
					public function bindValue(PHPExcel_Cell $cell, $value = null) { 
						// sanitize UTF-8 strings 
						if (is_string($value)) { 
							$value = PHPExcel_Shared_String::SanitizeUTF8($value); 
						} 

						// if it is a string and starts with 0, the value will be converted into string 
						if (is_string($value) && ($value[0] == '0' || $value[0] == '+')) {
							$cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING); 
							return true; 
						} 
						return parent::bindValue($cell, $value); 
					} 
				}
			
            Excel::load($path, function ($reader) {
                $reader->sheet('Worksheet', function ($sheet) {
                    $sheet->setColumnFormat(array('R' => \PHPExcel_Cell_DataType::TYPE_STRING));
                });
            })->download('xls', array('Content-Disposition' => 'attachment; filename=' . $prettyNames[$converter_type] . " - " . date('Y-m-d'). '.xls'));
            */
		}
    }
}