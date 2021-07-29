<?php

namespace Framework\Baseapp\Commands;

use Swoolecan\Foundation\Commands\TraitGenResourceCommand;

class GenResourceCommand extends AbstractCommand
{
    use TraitGenResourceCommand;

    protected $signature = 'gen:resource';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create classes for a resource';

    public function handle()
    {
        $type = $this->argument('type');
        $options = $this->option('options');
        file_put_contents('/tmp/text.txt', date('Y-m-d H:i:s') . '--'. $type. '==' . $options . 'ssssssssss', FILE_APPEND);
        echo 'sssssssssss';exit();
    }

    public function createResources($resources, $config)
    {
        //print_r($resources);
        foreach ($resources as $resourceBase => $elems) {
            $this->createResource($resourceBase, $elems, $config);
        }
    }

    protected function createResource($resourceBase, $elems, $config)
    {
        list($app, $resource) = explode('-', $resourceBase);
        foreach ($elems as $type => $elem) {
            if (in_array($app, ['merchant', 'third'])) {
                continue;
            }
            if (strpos($elem, 'Framework') === 0) {
                continue;
            }
            $basefile = substr($elem, strpos($elem, '\\') + 1);
            $basefile = str_replace('\\', '/', $basefile) . '.php';
            $file = $config['moduleBase'] . '/' . $app . '/app/' . $basefile;
            if (file_exists($file)) {
                continue;
            }
            echo $file . "\n";
            //continue;

            $namespace = substr($elem, 0, strrpos($elem, '\\'));
            $class = substr($elem, strrpos($elem, '\\') + 1);
            $this->_createClass($file, $namespace, $class, $type, $resource, $config);
        }
    }

    protected function _createClass($file, $namespace, $class, $type, $resource, $config)
    {
        $stubFile = $config['stubPath'] . '/' . $type . '.stub';
        $content = file_get_contents($stubFile);
        $table = $this->getResource()->strOperation($resource, 'snake');
        $content = str_replace(['%NAMESPACE%', '%CLASS%', '%TABLE%'], [$namespace, $class, $table], $content);
        $path = dirname($file);
        if (!is_dir($path)) {
            if (!is_dir(dirname($path))) {
                echo $path;exit();
                mkdir(dirname($path));
            }
            mkdir($path);
        }
        file_put_contents($file, $content);
    }
}
