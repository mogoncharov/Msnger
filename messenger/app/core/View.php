<?php 

namespace App\core;

class View
{
    public function render($contenView, $templateView = null, $payload = null)
    {
        if(isset($templateView)) {
            include_once LAYOUT . $templateView;
        }
    }
}