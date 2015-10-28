<?php
namespace Components\Providers\Slayer;

use Bootstrap\Adapters\Blade\BladeAdapter;
use Phalcon\Events\Manager as EventsManager;
use Bootstrap\Services\Service\ServiceProvider;
use Phalcon\Mvc\View\Engine\Php as PhalconEnginePhp;
use Bootstrap\Support\Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Volt as PhalconVoltEngine;

class View extends ServiceProvider
{
    protected $alias  = 'view';
    protected $shared = true;

    public function register()
    {
        $view = new PhalconView();
        $view->setViewsDir(config()->path->views);

        $view->registerEngines([

            '.volt'  =>
                function ($view, $di) {
                    $volt = new PhalconVoltEngine($view, $di);

                    $volt->setOptions([
                        'compiledPath'      => config()->path->storage_views,
                        'compiledSeparator' => '_',
                    ]);

                    $compiler = $volt->getCompiler();

                    # others
                    $compiler->addFunction('di', 'di');
                    $compiler->addFunction('env', 'env');
                    $compiler->addFunction('echo_pre', 'echo_pre');
                    $compiler->addFunction('csrf_field', 'csrf_field');
                    $compiler->addFunction('dd', 'dd');
                    $compiler->addFunction('config', 'config');

                    # facade
                    $compiler->addFunction('security', 'security');
                    $compiler->addFunction('tag', 'tag');
                    $compiler->addFunction('route', 'route');
                    $compiler->addFunction('response', 'response');
                    $compiler->addFunction('view', 'view');
                    $compiler->addFunction('config', 'config');
                    $compiler->addFunction('url', 'url');
                    $compiler->addFunction('request', 'request');

                    # paths
                    $compiler->addFunction('base_uri', 'base_uri');

                    return $volt;
                },

            '.phtml'     => PhalconEnginePhp::class,
            '.blade.php' => BladeAdapter::class,
        ]);

        # ---- instatiate a new event manager
        $event_manager = new EventsManager;

        # ---- after rendering the view
        # by default, we should destroy the flash
        $event_manager->attach("view:afterRender",
            function ($event, $dispatcher, $exception) {

                # - this should destroy the flash
                $flash = $dispatcher->getDI()->get('flash');
                $flash->destroy();
            }
        );

        $view->setEventsManager($event_manager);

        return $view;
    }
}
