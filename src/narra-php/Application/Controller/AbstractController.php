<?php

namespace NarraPhp\Application\Controller;

abstract class AbstractController
{
    const INDEX = 'index';
    const ACTION_POSTFIX = 'Action';
    
    protected $action;
    protected $params;
    protected $reflection;
    
    public function __construct($action, $params = [])
    {
        $class = get_class($this);
        
        $this->reflection = new \ReflectionMethod($class, $action . self::ACTION_POSTFIX);
        $this->params = $params;
    }
    
    public function run()
    {
        $this->reflection->invokeArgs($this, $this->params);
        
        return true;
    }
}