<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getAllPaniers(){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('pa.id','pa.quantite','pa.prix','pa.dateAjoutPanier','pa.user_id','pa.produit_id','pa.commande_id')
            ->from('paniers','pa')
            ->orderBy('pa.id','ASC');
        return $queryBuilder->execute()->fetchAll();
    }
}