<?php

namespace Framework\Baseapp\Commands;

use Swoolecan\Foundation\Commands\TraitGenResourceCommand;
use Swoolecan\Foundation\Services\TraitDatabaseService;

class GenResourceCommand extends AbstractCommand
{
    use TraitGenResourceCommand;
    use TraitDatabaseService;

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
        //print_r($resources);exit();
        foreach ($resources as $resourceBase => $elems) {
            $this->createResource($resourceBase, $elems, $config);
        }
    }

    public function checkResource($databases, $config)
    {
        $validDatabases = ['mysql', 'wmsystem'];
        $validDatabases = ['wmsystem'];
        $correspondApps = ['mysql' => 'passport'];
        $correspondTables = [
            'passport' => ['auth-manager' => 'manager', 'auth-managerlog' => 'managerlog', 'auth-permission' => 'permission', 'auth-resource' => 'resource', 'auth-role' => 'role', 'auth-role-manager' => 'role-manager', 'auth-role-permission' => 'role-permission'],
        ];
        $resourceSql = "INSERT INTO `wp_auth_resource` (`app`, `code`, `name`, `controller`, `request`, `model`, `service`, `repository`, `resource`, `collection`, `created_at`, `updated_at`) VALUES \n";
        $permissionSql = "INSERT INTO `wp_auth_permission` ( `code`, `resource_code`, `parent_code`, `name`, `app`, `controller`, `action`, `method`, `orderlist`, `display`, `icon`, `extparam`, `created_at`, `updated_at`) VALUES \n";
        $ignores = ['passport' => ['migrations']];
        foreach ($validDatabases as $database) {
            $info = $databases[$database];
            $tables = $this->getTableDatas($info['database'], $database);
            foreach ($tables as $table => $tData) {
                $table = str_replace($info['prefix'], '', $table);
                $table = str_replace('_', '-', $table);
                $app = $correspondApps[$database] ?? $database;
                $table = isset($correspondTables[$app]) && isset($correspondTables[$app][$table]) ? $correspondTables[$app][$table] : $table;
                if (isset($ignores[$app]) && in_array($table, $ignores[$app])) {
                    continue;
                }

                $resourceInfo = \DB::SELECT("SELECT * FROM `wp_auth_resource` WHERE `code` = '{$table}' AND `app` = '{$app}'");
                $resourceSql .= $this->_checkResource($resourceInfo, $table, $app, $tData['comment']);
                if (isset($resourceInfo[0]) && $resourceInfo[0]->controller) {
                    $datas = $this->_createFront($app, $table, $config);
                    $permissionSql .= $this->_checkPermission($table, $app, $tData['comment']);
                }
            }
        }
        echo $resourceSql;
        echo $permissionSql;
        exit();
    }

    protected function _checkResource($info, $table, $app, $tableName)
    {
        if ($info) {
            //echo 'exist - ' . $app . '==' . $table . "\n";
            return '';
        } else {
            //echo 'nooo - ' . $app . '==' . $table . "\n";
            return "('{$app}', '{$table}', '{$tableName}', '1', '1', '1', '', '1', '1', '1', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n";
        }
        //print_R($info);exit();
    }

    protected function _checkPermission($table, $app, $tableName)
    {
        $info = \DB::SELECT("SELECT * FROM `wp_auth_permission` WHERE `resource_code` = '{$table}' AND `app` = '{$app}'");
        if ($info) {
            //echo 'exist - pppppppp-' . $app . '==' . $table . "\n";
        } else {
            //echo 'nooo - pppppppp-' . $app . '==' . $table . "\n";
            $sql = "('{$app}_{$table}_add', '{$table}', 'PARENTCODE', '添加{$tableName}', '{$app}', '{$table}', 'add', 'post', 0, 4, '', '', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n";
            $sql .= "('{$app}_{$table}_delete', '{$table}', 'PARENTCODE', '删除', '{$app}', '{$table}', 'delete', 'delete', 0, 5, '', '', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n";
            $sql .= "('{$app}_{$table}_update', '{$table}', 'PARENTCODE', '编辑', '{$app}', '{$table}', 'update', 'put', 0, 5, '', '', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n";
            //$sql .= "('{$app}_{$table}_view', '{$table}', 'PARENTCODE', '查看', '{$app}', '{$table}', 'view', 'get', 0, 5, '', '', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n";
            $sql .= "('{$app}_{$table}_listinfo', '{$table}', 'PARENTCODE', '{$tableName}', '{$app}', '{$table}', 'listinfo', 'get', 99, 3, '', '', '{$this->currentTimestamp()}', '{$this->currentTimestamp()}'), \n \n";
            return $sql;
        }
        //print_R($info);exit();
    }

    protected function createResource($resourceBase, $elems, $config)
    {
        list($app, $resource) = explode('-', $resourceBase);
        foreach ($elems as $type => $elem) {
            if (in_array($app, ['passport'])) {
                continue;
            }
            if (strpos($elem, 'Framework') === 0) {
                continue;
            }
            $basefile = substr($elem, strpos($elem, '\\') + 1);
            $basefile = str_replace('\\', '/', $basefile) . '.php';
            $file = $config['moduleBase'] . '/' . $app . '/app/' . $basefile;
            $namespace = substr($elem, 0, strrpos($elem, '\\'));
            $class = substr($elem, strrpos($elem, '\\') + 1);
            if (file_exists($file)) {
                //$this->changeApp($app, $file, $namespace, $class, $type, $resource, $config);
                continue;
            }
            echo $file . "\n";
            //continue;

            $this->_createClass($file, $namespace, $class, $type, $resource, $config);
        }
    }

    protected function _createFront($app, $resource, $config)
    {
        static $datas;

        $resource = $this->getResource()->strOperation($resource, 'snake', '-');
        $class = $this->getResource()->strOperation($resource, 'studly');
        $plural = $this->getResource()->strOperation($resource, 'plural');
        $camel = $this->getResource()->strOperation($plural, 'camel');
        $file = $config['frontPath'] . '/' . $app . '/' . $class . '.js';
        $mark = isset($datas['all']) && in_array($resource, $datas['all']) ? $app . ucfirst($camel) : $camel;
        $stubFile = $config['stubPath'] . '/front.stub';
        $content = file_get_contents($stubFile);
        $content = str_replace(['{{APP}}', '{{RESOURCE}}', '{{URESOURCE}}', '{{RESOURCEMARK}}'], [$app, $plural, $class, $mark], $content);
        if (!file_exists($file)) {
            file_put_contents($file, $content);
        }
        
        echo $plural . '---' . $camel . '-==' . $class . '==' . $app . '==' . $resource . '-=-=' . $file . "===\n";
        $datas[$app]['file'][] = "import {$class} from '@/applications/{$app}/{$class}'\n";
        $datas[$app]['class'][] = $class;
        $datas['all'][] = $resource;
        $dContent = implode('', $datas[$app]['file']);
        $dContent .= "\nexport default {" . implode(', ', $datas[$app]['class']) . '}';
        $dFile = $config['frontPath'] . '/' . $app . '/database.js';
        file_put_contents($dFile, $dContent);
        return $datas;
    }

    protected function _createClass($file, $namespace, $class, $type, $resource, $config)
    {
        $stubFile = $config['stubPath'] . '/' . $type . '.stub';
        $content = file_get_contents($stubFile);
        $table = $this->getResource()->strOperation($resource, 'snake');

        $fieldStr = $type == 'repository' ? $this->getPointField('wmsystem', $table, 'string') : '';
        $content = str_replace(['%NAMESPACE%', '%CLASS%', '%TABLE%', '%FIELDSTR%'], [$namespace, $class, $table, $fieldStr], $content);
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

    protected function getPointField($connection, $table, $returnType = 'array')
    {
        $db = \DB::connection($connection);
        $config = $db->getConfig();
        $str = '';
        $where['TABLE_NAME'] = $config['prefix'] . $table;
        $columes = $db->table(\DB::raw('information_schema.COLUMNS'))->where($where)->get();
        $data = [];
        foreach ($columes as $colume) {
            $data[$colume->COLUMN_NAME] = $colume->COLUMN_COMMENT;
        }
        if ($returnType == 'string') {
            return "'" . implode("', '", array_keys($data)) . "'";
        }
        return $data;
    }

    protected function currentTimestamp()
    {
        return date('Y-m-d H:i:s');
    }
}
