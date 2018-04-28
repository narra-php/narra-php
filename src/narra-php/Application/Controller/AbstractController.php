<?php

namespace NarraPhp\Application\Controller;

use NarraPhp\Application\View\Template;

abstract class AbstractController
{
    const INDEX = 'index';
    const ACTION_POSTFIX = 'Action';
    
    protected $action;
    protected $params;
    protected $reflection;
    protected $template;
    
    public function __construct(
        $action, $params = [], $template
    ) {
        $class = get_class($this);
        
        $this->reflection = new \ReflectionMethod($class, $action . self::ACTION_POSTFIX);
        $this->params = $params;
        $this->template = $template;
    }
    
    public function run()
    {
        $renderData = $this->reflection->invokeArgs($this, $this->params);
        
        // call view rendering class
        return $this->template->setRenderData($renderData)->render();
    }
}