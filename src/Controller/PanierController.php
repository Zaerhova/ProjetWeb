<?php

namespace App\Controller;

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

        return $controllers;
    }
}