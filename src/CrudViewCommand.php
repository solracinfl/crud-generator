<?php

namespace Appzcoder\CrudGenerator;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudViewCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create view files for crud operation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $crudName = strtolower($this->argument('name'));
        // $crudNameCap = ucwords($crudName);
        $crudNameCap = $this->argument('name');
        $crudNameSingular = str_singular($crudName);
        //$crudNameSingularCap = ucwords($crudNameSingular);
        $crudNameSingularCap = $crudNameSingular;
        // $crudNamePlural = str_plural($crudName);
        $crudNamePlural = str_singular($crudName);
        //$crudNamePluralCap = ucwords($crudNamePlural);
        $crudNamePluralCap = $crudNamePlural;
        $viewDirectory = base_path('resources/views/');
        $rulesPath = base_path('app/Http/Requests/');
        $path = $viewDirectory . $crudName . '/';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $fields = $this->option('fields');

        $page_title = $this->argument('title');
        $key_field = $this->argument('key');
        $table_name = $this->argument('table_name');

        $fields = $this->option('fields');
        $fieldsArray = explode(',', $fields);

        $formFields = array();
        $x = 0;
        foreach ($fieldsArray as $item) {
            $array = explode(':', $item);
            $formFields[$x]['name'] = trim($array[0]);
            $formFields[$x]['type'] = trim($array[1]);
            $x++;
        }

        $formFieldsHtml = '';
        $indexColumns = '';
        foreach ($formFields as $item) {
            $label = ucwords(strtolower(str_replace('_', ' ', $item['name'])));
            $formFieldsHtml .= "<div class=" . '"row"' . ">" . "<div class=" . '"' . 'form-group'. '"' . ">". PHP_EOL;
            $formFieldsHtml .= "    {!! Form::label('" . $item['name'] . "', '" . $label . " ', ['class' => 'control-label']) !!}" . PHP_EOL;

            if(strlen($indexColumns)>0)
                $indexColumns = $indexColumns . ',';
            $indexColumns = $indexColumns . 'null';

            if ($item['type'] == 'string') {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'bigstring') {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'512','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'date') {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255', 'placeholder'=>'mm/dd/yyy'],'id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'phone') {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255', 'placeholder'=>'(###) ###-####','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'amount') {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255', 'placeholder'=>'####.##','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'text') {
                $formFieldsHtml .= "    {!! Form::textarea('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'2048','id'=>'" . $item['name'] . "']) !!}" . PHP_EOL;
            }
            elseif ($item['type'] == 'password') {
                $formFieldsHtml .= "    {!! Form::password('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'45','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'email') {
                $formFieldsHtml .= "    {!! Form::email('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'integer') {
                $formFieldsHtml .= "    {!! Form::number('" . $item['name'] . "', null, ['class' => 'form-control', 'maxlength'=>'255', 'placeholder'=>'####.##','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'checkbox') {
                $formFieldsHtml .= "    {!! Form::hidden('" . $item['name'] . "', 0) !!}". PHP_EOL;
                $formFieldsHtml .= "    {!! Form::checkbox('" . $item['name'] . "') !!}". PHP_EOL;
            }
            elseif ($item['type'] == 'select') {
                $formFieldsHtml .= "    {!! Form::select('" . $item['name'] . "', $". $item['name'] . ", null, ['class' => 'form-control input75','id'=>'" . $item['name'] . "']) !!}" . PHP_EOL;
            }

            else {
                $formFieldsHtml .= "    {!! Form::text('" . $item['name'] . "', null, ['class' => 'form-control input75', 'maxlength'=>'255','id'=>'" . $item['name'] . "']) !!}". PHP_EOL;
            }
            $formFieldsHtml .="</div></div>". PHP_EOL;

        }

        // Form fields and label
        $formHeadingHtml = '';
        $formBodyHtml = '';
        $formBodyHtmlForShowView = '';
        $formBodyHtmlForShowOnlyView = '';

        $crudRules = '';

        $i = 0;
        foreach ($formFields as $key => $value) {
            if ($i == 3) {
                break;
            }

            $field = $value['name'];
            $label = ucwords(str_replace('_', ' ', $field));
            $formHeadingHtml .= '<th>' . $label . '</th>';

            if ($i == 0) {
                $formBodyHtml .= '<td><a href="{{ url(\'/%%crudName%%\', $item->' . $key_field . ') }}">{{ $item->' . $field . ' }}</a></td>';
            } else {
                $formBodyHtml .= '<td>{{ $item->' . $field . ' }}</td>';
            }
            $formBodyHtmlForShowView .= '<td> {{ $%%crudNameSingular%%->' . $field . ' }} </td>';

            $i++;
        }

        foreach ($formFields as $key => $value) {
            $field = $value['name'];
            $label = ucwords(str_replace('_', ' ', $field));
            $formBodyHtmlForShowOnlyView .= '<tr><td> ' . $label . '</td></tr>'. PHP_EOL . "          ";
            $formBodyHtmlForShowOnlyView .= '<tr><td> {{ $%%crudNameSingular%%->' . $field . ' }} </td></tr>'. PHP_EOL . "          ";
            $crudRules .= "'" . $field ."'  => 'required', " . PHP_EOL . "          ";
        }


        // For index.blade.php file
        $indexFile = __DIR__ . '/stubs/index.blade.stub';
        $newIndexFile = $path . 'index.blade.php';
        if(file_exists($newIndexFile))
        {
            $this->error('File already exist ' . $newIndexFile);
        }
        else
        {
            if (!copy($indexFile, $newIndexFile)) {
                echo "failed to copy $indexFile...\n";
            } else {
                file_put_contents($newIndexFile, str_replace('%%title%%', $page_title, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%key%%', $key_field, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%formHeadingHtml%%', $formHeadingHtml, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%formBodyHtml%%', $formBodyHtml, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%crudName%%', $crudName, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%crudNameCap%%', $crudNameCap, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%crudNamePlural%%', $crudNamePlural, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%crudNamePluralCap%%', $crudNamePluralCap, file_get_contents($newIndexFile)));
                file_put_contents($newIndexFile, str_replace('%%indexColumns%%', $indexColumns, file_get_contents($newIndexFile)));

            }
        }

        // For create.blade.php file
        $createFile = __DIR__ . '/stubs/create.blade.stub';
        $newCreateFile = $path . 'create.blade.php';
        if(file_exists($newCreateFile))
        {
            $this->error('File already exist ' . $newCreateFile);
        }
        else {

            if (!copy($createFile, $newCreateFile)) {
                echo "failed to copy $createFile...\n";
            } else {
                file_put_contents($newCreateFile, str_replace('%%title%%', $page_title, file_get_contents($newCreateFile)));
                file_put_contents($newCreateFile, str_replace('%%key%%', $key_field, file_get_contents($newCreateFile)));
                file_put_contents($newCreateFile, str_replace('%%crudName%%', $crudName, file_get_contents($newCreateFile)));
                file_put_contents($newCreateFile, str_replace('%%crudNameSingularCap%%', $crudNameSingularCap, file_get_contents($newCreateFile)));

            }
        }
        // For edit.blade.php file
        $editFile = __DIR__ . '/stubs/edit.blade.stub';
        $newEditFile = $path . 'edit.blade.php';
        if(file_exists($newEditFile))
        {
            $this->error('File already exist ' . $newEditFile);
        }
        else {

            if (!copy($editFile, $newEditFile)) {
                echo "failed to copy $editFile...\n";
            } else {
                file_put_contents($newEditFile, str_replace('%%title%%', $page_title, file_get_contents($newEditFile)));
                file_put_contents($newEditFile, str_replace('%%key%%', $key_field, file_get_contents($newEditFile)));
                file_put_contents($newEditFile, str_replace('%%crudNameCap%%', $crudNameCap, file_get_contents($newEditFile)));
                file_put_contents($newEditFile, str_replace('%%crudName%%', $crudName, file_get_contents($newEditFile)));
                file_put_contents($newEditFile, str_replace('%%crudNameSingular%%', $crudNameSingular, file_get_contents($newEditFile)));
                file_put_contents($newEditFile, str_replace('%%crudNameSingularCap%%', $crudNameSingularCap, file_get_contents($newEditFile)));

            }
        }
        // For show.blade.php file
        $showFile = __DIR__ . '/stubs/show.blade.stub';
        $newShowFile = $path . 'show.blade.php';
        if(file_exists($newShowFile))
        {
            $this->error('File already exist ' . $newShowFile);
        }
        else {

            if (!copy($showFile, $newShowFile)) {
                echo "failed to copy $showFile...\n";
            } else {
                file_put_contents($newShowFile, str_replace('%%title%%', $page_title, file_get_contents($newShowFile)));
                file_put_contents($newShowFile, str_replace('%%formBodyHtmlForShowOnlyView%%', $formBodyHtmlForShowOnlyView, file_get_contents($newShowFile)));
                file_put_contents($newShowFile, str_replace('%%crudNameSingular%%', $crudNameSingular, file_get_contents($newShowFile)));
                file_put_contents($newShowFile, str_replace('%%crudNameSingularCap%%', $crudNameSingularCap, file_get_contents($newShowFile)));

            }
        }
        // For show.blade.php file

        $formFieldsHtml = $formFieldsHtml . '@section("view_script")'. PHP_EOL;
        $formFieldsHtml = $formFieldsHtml . '<script language="JavaScript">'. PHP_EOL;
        $formFieldsHtml = $formFieldsHtml . '     $(document).ready(function() {'. PHP_EOL;

        foreach ($formFields as $item) {
            $label = ucwords(strtolower(str_replace('_', ' ', $item['name'])));

            if ($item['type'] == 'date') {
                $formFieldsHtml .= " $('#" . $item['name'] . "').mask('00/00/0000');". PHP_EOL;
                $formFieldsHtml .= " $('#" . $item['name'] . "').datepicker({ autoclose: true});". PHP_EOL;
            }
            elseif ($item['type'] == 'phone') {
                $formFieldsHtml .= " $('#" . $item['name'] . "').mask('(000) 000-0000');". PHP_EOL;
            }
            elseif ($item['type'] == 'amount') {
                $formFieldsHtml .= " $('#" . $item['name'] . "').mask('000000000.00');". PHP_EOL;
            }
            elseif ($item['type'] == 'integer') {
                $formFieldsHtml .= " $('#" . $item['name'] . "').mask('000000000');". PHP_EOL;
            }
            $formFieldsHtml .="". PHP_EOL;
        }
        $formFieldsHtml = $formFieldsHtml . '     });'. PHP_EOL;
        $formFieldsHtml = $formFieldsHtml . '</script>'. PHP_EOL;
        $formFieldsHtml = $formFieldsHtml . '@stop'. PHP_EOL;


        $newTemplatefile = $path . '_form.blade.php';
        if(file_exists($newTemplatefile))
        {
            $this->error('File already exist ' . $newTemplatefile);
        }
        else {
           file_put_contents($newTemplatefile, $formFieldsHtml);
        }
        // For layouts/master.blade.php file
        $layoutsDirPath = base_path('resources/views/layouts/');
        if (!is_dir($layoutsDirPath)) {
            mkdir($layoutsDirPath);
        }
        $this->info('Form layout created successfully.');

        $layoutsFile = __DIR__ . '/stubs/master.blade.stub';
        $newLayoutsFile = $layoutsDirPath . 'master.blade.php';

        if (!file_exists($newLayoutsFile)) {
            if (!copy($layoutsFile, $newLayoutsFile)) {
                echo "failed to copy $layoutsFile...\n";
            } else {
                file_get_contents($newLayoutsFile);
            }
        }

        $this->info('Views created successfully.');

        //
        // For request (CF: CREATE A DEFAULT REQUEST RULES)
        //
        $requestFile = __DIR__ . '/stubs/request.stub';
        $newRequestFile = $rulesPath . $crudNameCap .'Request.php';
        if(file_exists($newRequestFile))
        {
            $this->error('File already exist ' . $newRequestFile);
        }
        else {


            if (!copy($requestFile, $newRequestFile)) {
                echo "failed to copy $newRequestFile...\n";
            } else {
                file_put_contents($newRequestFile, str_replace('%%crudNameSingular%%', $crudNameCap, file_get_contents($newRequestFile)));
                file_put_contents($newRequestFile, str_replace('%%crudRules%%', $crudRules, file_get_contents($newRequestFile)));
            }
        }
        $this->info('Request Rules created successfully.');





    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of the Crud.'],
            ['title',  InputArgument::REQUIRED, 'Title for the page.'],
            ['key',  InputArgument::REQUIRED, 'Key for the object.'],
            ['table_name', InputArgument::REQUIRED, 'table name for object.'],

        ];
    }

    /*
     * Get the console command options.
     *
     * @return array
     */

    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'The fields of the form.', null],
        ];
    }

}
