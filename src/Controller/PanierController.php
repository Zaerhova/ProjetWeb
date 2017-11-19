<?php

namespace App\Controller;

use App\Model\CommandeModel;
use App\Model\UserModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\PanierModel;
use App\Model\ProduitModel;
use App\Model\TypeProduitModel;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class PanierController implements ControllerProviderInterface
{

    private $panierModel;
    private $produitModel;
    private $userModel;
    private $commandeModel;

    public function index(Application $app) {
        return $this->showPanier($app);
    }

    public function showPanier(Application $app) {
        $this->panierModel = new PanierModel($app);
        $this->produitModel = new ProduitModel($app);
        $paniers = $this->panierModel->getAllPaniers();
        $produits = $this->produitModel->getAllProduits();
        return $app["twig"]->render('frontOff/showPanierUser.html.twig',['dataProduit'=>$produits,'dataPanier'=>$paniers]);
    }

    public function addPanier(Application $app, $id){
        $this->produitModel = new ProduitModel($app);
        $produits = $this->produitModel->getAllProduits();
        $donnees = $this->produitModel->getProduit($id);
        $donnees['user_id'] = $app['session']->get('user_id');
        $donnees['dateAjoutPanier'] = date('y-m-d h:m:s');
        $donnees['etat_id'] = 1;
        $this->commandeModel = new CommandeModel($app);
        $this->commandeModel->addCommande($donnees);
        $donnees += $this->commandeModel->getCommande($donnees);
        $this->panierModel = new PanierModel($app);
        $this->panierModel->addPanier($donnees);
        $paniers = $this->panierModel->getAllPaniers();

        return $app["twig"]->render('frontOff/showPanierUser.html.twig',['dataProduit'=>$produits,'dataPanier'=>$paniers]);
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

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::showPanier')->bind('panier.showProduits');

        $controllers->post('/add{id}', 'App\Controller\PanierController::addPanier')->bind('panier.add');

        return $controllers;
    }
}