<?php

namespace NarraPhp\Application\View;

class Template 
{
    protected $viewPath;
    protected $renderData;
    
    public function __construct($viewPath)
    {
        if( is_null($viewPath) || strlen( trim($viewPath) ) < 1 ) {
            echo "No template defined!";
            die();
        }
            
        $this->viewPath = $viewPath;
    }
    
    public function setRenderData($renderData)
    {
        $this->renderData = $renderData;
        
        return $this;
    }
    
    public function render()
    {
        extract($this->renderData);
        include_once($this->viewPath);
        
        return true;
    }
}