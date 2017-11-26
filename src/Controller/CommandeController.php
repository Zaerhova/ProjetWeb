<?php

namespace App\Controller;

use App\Model\CommandeModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\PanierModel;
use App\Model\ProduitModel;


use Symfony\Component\Security;

class CommandeController implements ControllerProviderInterface
{

    private $panierModel;
    private $produitModel;
    private $commandeModel;

    public function index(Application $app)
    {
        if ($app['session']->get('roles') == 'ROLE_CLIENT') {
            return $this->showCommandeUser($app);
        }
        else if($app['session']->get('roles') == 'ROLE_ADMIN') {
            return $this->showCommande($app);
        }
        else return $app->redirect($app["url_generator"]->generate("panier.index"));


    }

    public function showCommandeUser(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $user_id =  $app['session']->get('user_id');
        $commandeUser = $this->commandeModel->getCommandeUser($user_id);
        return $app["twig"]->render('frontOff/showCommandeUser.html.twig',['commandeUser'=>$commandeUser]);
    }

    public function showCommande(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $commandeUser = $this->commandeModel->getAllCommande();
        return $app["twig"]->render('backOff/Commande/showCommande.html.twig',['commandeUser'=>$commandeUser]);
    }

    public function updateCommande(Application $app,$id){
        $this->commandeModel = new CommandeModel($app);
        $this->commandeModel->updateCommande($id);
        return $app->redirect($app["url_generator"]->generate("commande.index"));

    }

    public function detailCommande(Application $app,$id){
        $this->commandeModel = new CommandeModel($app);
        $commandeUser = $this->commandeModel->getCommande($id);
        return $app["twig"]->render('frontOff/showDetailCommande.html.twig',['commandeUser'=>$commandeUser]);
    }










    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\CommandeController::index')->bind('commande.index');
        $controllers->get('/show', 'App\Controller\CommandeController::showCommandeUser')->bind('commande.showProduits');
        $controllers->put('/update{id}', 'App\Controller\CommandeController::updateCommande')->bind('commande.update');
        $controllers->get('/detail{id}', 'App\Controller\CommandeController::detailCommande')->bind('commande.detail');


        return $controllers;
    }
}