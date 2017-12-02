<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0


class IndexController implements ControllerProviderInterface
{
    public function index(Application $app)
    {
        if ($app['session']->get('roles') == 'ROLE_CLIENT')
          return $app->redirect($app["url_generator"]->generate("panier.index"));
        if ($app['session']->get('roles') == 'ROLE_ADMIN')
            return $app["twig"]->render("backOff/backOFFICE.html.twig");
        // remplacer par une redirection
        
        return $app["twig"]->render("accueil.html.twig");
    }

    public function errorCsrf(Application $app){
        return $app['twig']->render("v_error_csrf.html.twig");
    }

    public function errorDroit(Application $app){
        return $app['twig']->render("v_error_droit.html.twig");
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\IndexController::index')->bind('accueil');
        $index->get("/error",'App\Controller\IndexController::errorCsrf')->bind('index.errorCsrf');
        $index->get("/errorDroit",'App\Controller\IndexController::errorDroit')->bind('index.errorDroit');
        return $index;
    }


}
