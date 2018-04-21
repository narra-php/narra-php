<?php

namespace NarraPhp;

use Symfony\Component\HttpFoundation\Request;

class Application
{
    const ADMIN = 'admin';
    const INDEX = 'index';
    
    protected $registeredModules;
    protected $baseRoutes;
    protected $autoload;
    
    public function __construct(
        \Composer\Autoload\ClassLoader $autoload
    ) {
        // Define path constants
    
        define("DS", DIRECTORY_SEPARATOR);
        define("NS", '\\');
        
        define("ROOT", getcwd() . DS);
        
        define("APP_PATH", ROOT . "app" . DS);
            define("MODULES_PATH", APP_PATH . "modules" . DS);
                define("MODULE_CONFIG_PATH", "config" . DS);
                    define("MODULE_ROUTE_CONFIG_FILE",MODULE_CONFIG_PATH . "routes.php");
                define("MODULE_CONTROLLER_PATH", "Controller" . NS);
                    define("MODULE_CONTROLLER_HOME_PATH", MODULE_CONTROLLER_PATH . "Home" . NS);
        //         define("MODULE_MODEL_PATH", "Model" . DS);
        //         define("MODULE_VIEW_PATH", "views" . DS);
        //     define("THEMES_PATH", APP_PATH . "themes" . DS);
             define("APP_ETC_PATH", APP_PATH . "etc" . DS);
                define("REGISTER_PATH", APP_ETC_PATH . "register" . DS);
    
        // define("PUBLIC_PATH", ROOT . "public" . DS);
        //     define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);
        
        // Configuration files
        $GLOBALS['modules_register'] = REGISTER_PATH . "modules.php";
        $GLOBALS['themes_register'] = REGISTER_PATH . "themes.php";
        
        // call composer autoload
        $this->autoload = $autoload;
    
        // Start session
        session_start();
    }
    
    public function run()
    {
        return $this->autoload()->dispatch();
    }

    private function autoload()
    {
        // load only necessary classes listed in the app/etc/register/modules.php
        $modules = require_once $GLOBALS['modules_register'];
        
        // register modules
        foreach($modules as $namespace => $enabled) {
            // if enabled, load module
            if($enabled) {
                // convention: app/modules/VendorName/ModuleName
                $vendorName = strtok($namespace, NS);
                $moduleName = strtok(NS);
                $modulePath = MODULES_PATH . $vendorName . DS . $moduleName;
                
                if(!file_exists($modulePath)) {
                    throw new \Exception("Can't find module: ". $namespace);
                }
                
                $this->autoload->addPsr4($namespace . NS, $modulePath);
                
                // load config files
                $this->registeredModules[] = $namespace;
                
                $routeConfig = include $modulePath . DS . MODULE_ROUTE_CONFIG_FILE;
                
                if($routeConfig && isset($routeConfig['base_route']) && $routeConfig['base_route']) {
                    $this->baseRoutes[$routeConfig['base_route']] = $namespace;
                }
            }
        }
        
        return $this;
    }

    private function dispatch()
    {
        $requestInfo = [];
        
        $request = Request::createFromGlobals();
        $requestUri = $request->getRequestUri();
        
        $rawUrlFrags = explode(DS,trim($requestUri,DS));
        
        if(count($rawUrlFrags) <= 1) {
            die('Home page');
        }
        
        if($rawUrlFrags[0] === self::ADMIN) {
            die('ADMIN'); // skip admin for the meantime
        }
        
        $controllerClass = $this->baseRoutes[$rawUrlFrags[0]] . NS . MODULE_CONTROLLER_HOME_PATH . ucfirst(strtolower($rawUrlFrags[1]));
        $action = isset($rawUrlFrags[2]) ? $rawUrlFrags[2] : INDEX;
        
        // create new instance of the requested controller
        $controllerObject = new $controllerClass($action,array_slice($rawUrlFrags,3));
        
        // run the requested action
        $controllerObject->run();
        
        return $this;

    }
}