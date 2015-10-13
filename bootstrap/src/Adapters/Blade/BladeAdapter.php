<?php

namespace Bootstrap\Adapters\Blade;

use Bootstrap\Support\Illuminate\View\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Phalcon\Mvc\View\Engine;

class BladeAdapter extends Engine
{
    protected $view;
    protected $blade;

    public function __construct($view, $di)
    {
        parent::__construct($view, $di);

        $this->blade_engine = new CompilerEngine(
            new BladeCompiler( new Filesystem, config()->path->storage_views)
        );

        $this->view = $view;
    }

    protected function compiler()
    {
        return $this->blade_engine->getCompiler();
    }

    public function render($path, $params = [])
    {
        # - let's check if the blade file time is different
        # from the compiled file

        if ($this->compiler()->isExpired($path)) {
            $this->compiler()->compile($path);
        }

        # - now buffer the compiled template to get the php variables
        # and also declare under the buffer about the parameters.

        ob_start();

        if ( !empty($params) ) {
            foreach ($params as $key => $value) {
                ${$key} = $value;
            }
        }

        $__env = new Factory($this);

        include $this->compiler()->getCompiledPath($path);

        di()->get('view')->setContent(ob_get_clean());
    }
}