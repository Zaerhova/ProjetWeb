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

    public function addPanier(Application $app, $id){
        $compteur = 0;
        $this->produitModel = new ProduitModel($app);
        $donnees = $this->produitModel->getProduit($id);
        $donnees['user_id'] = $app['session']->get('user_id');
        $donnees['dateAjoutPanier'] = date('y-m-d h:m:s');
        $this->panierModel = new PanierModel($app);
        $paniers = $this->panierModel->getAllPaniers();
        if (empty($paniers)){
            $this->panierModel->addPanier($donnees);
        }else {
            foreach ($paniers as $panier) {
                if ($panier['produit_id'] == $id && !isset($panier['commande_id']) ) {
                    $this->panierModel->addQuantite($panier['id']);
                } else $compteur++;
            }
            if (sizeof($paniers) == $compteur) {
                $this->panierModel->addPanier($donnees);
            }
        }
        return $app->redirect($app["url_generator"]->generate("panier.index"));
    }

    public function deletePanier(Application $app, $id) {
        $this->panierModel = new PanierModel($app);
        $this->produitModel = new ProduitModel($app);
        $this->panierModel->deletePanier($id);
        return $app->redirect($app["url_generator"]->generate("panier.index"));

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

        $controllers->post('/delete{id}','App\Controller\PanierController::deletePanier')->bind('panier.delete');



        return $controllers;
    }
}