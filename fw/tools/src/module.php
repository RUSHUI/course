<?php
use ORC\DBAL\DBAL;
use ORC\Exception\SystemException;
class Module {
    public function register($fileinfo) {
        //parse module
        if (!($fileinfo instanceof \SplFileInfo)) {
            $fileinfo = new \SplFileInfo($fileinfo);
        }
        echo sprintf("scan module %s in folder %s\n", $fileinfo->getFilename(), $fileinfo->getPathname());
        $info_filename = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'module.info';
        $module_info = parse_ini_file($info_filename);
        if (!isset($module_info['c_name'])) {
            $module_info['c_name'] = $fileinfo->getFilename();
        }
        $dbal = DBAL::insert(\ORC\APP\Module::TABLENAME_MODULE);
        $dbal->set('name', $module_info['name']);
        $dbal->set('c_name', $module_info['c_name']);
        $dbal->set('description', empty($module_info['description']) ? '' : $module_info['description']);
        $dbal->set('dependence', empty($module_info['dependence']) ? '' : implode(',', $module_info['dependence']));
        //$dbal->set('permissions', empty($module_info['permissions']) ? '' : implode(',', $module_info['permissions']));
        $dbal->setDuplicate(array('name', 'description', 'dependence'));
        $dbal->execute();
        echo "\tdb done.\n";
        if (!empty($module_info['permissions'])) {
            $dbal = DBAL::insert(\ORC\APP\Module::TABLENAME_PERMS);
            foreach ($module_info['permissions'] as $perm) {
                $dbal->set('perm', $perm);
                $dbal->setDuplicate('perm');
                $dbal->execute();
            }
            echo "\tpermission done.\n";
        }
        //check actions
        $folder = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'actions';
        $this->scanActions($module_info['c_name'], $folder);
    }
    
    public function scan() {
        $path = DIR_APP_MODULE_ROOT;
        $this->scanPath($path);
    }
    
    public function route($path, $module, $action_name) {
        
    }
    protected function scanPath($path) {
        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot()) continue;
            if ($fileinfo->isDir()) {
                $this->scanPath($fileinfo->getPathname());
                continue;
            }
            if ($fileinfo->getFilename() == 'module.info') {
                $this->register($fileinfo->getPath());//found a module
                break;
            }
        }
    }
    
    protected function scanActions($module, $path, $extra_folder = '') {
        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot()) continue;
            if ($fileinfo->isDir()) {
                $this->scanActions($module, $fileinfo->getPathname(), $extra_folder . DIRECTORY_SEPARATOR . $fileinfo->getFilename());
                continue;
            }
            $filename = $fileinfo->getFilename();
            if (preg_match('/^([a-z0-9_]+)\.action\.php$/i', $filename, $matches)) {
                $action_name = ltrim(str_replace(DIRECTORY_SEPARATOR, '_', $extra_folder) . '_' . $matches[1], '_');
                $class_name = sprintf('%s_%s_Action', $module, $action_name);
                //user a parser or not
                //var_dump($class_name, class_exists($class_name));
                //@TODO not implement, will user route annotation later
                $action_info = array();
                $action_info['action_name'] = str_replace('_', '.', $action_name);
                $action_info['module'] = $module;
                $action_info['url'] = sprintf('/%s/%s', $module, str_replace('_', '/', $action_name));
                $action_info['filename'] = substr($fileinfo->getPathname(), strlen(DIR_APP_MODULE_ROOT) + 1);
                //var_dump($action_info);
//                 require_once $fileinfo->getPathname();
//                 $ref = new ReflectionClass($class_name);
//                 $doc = $ref->getDocComment();
//                 $doc = explode(PHP_EOL, $doc);
//                 foreach ($doc as $line) {
//                     $line = trim($line);
//                     if (preg_match('/[\s*]+@Route\s*(.*)$/i', $line, $matches)) {
                        
//                     }
//                 }
                $dbal = DBAL::insert(\ORC\Util\Route::TABLENAME_ROUTES);
                $dbal->set($action_info);
                $dbal->setDuplicate(array('action_name', 'module', 'filename'));
                $dbal->execute();
            }
        }
    }
}