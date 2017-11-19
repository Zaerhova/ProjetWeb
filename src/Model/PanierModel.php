<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use function MongoDB\BSON\fromJSON;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getAllPaniers(){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('pa.id','count(*) as quantite','pa.prix','pa.dateAjoutPanier','pa.user_id','pa.produit_id','pa.commande_id')
            ->from('paniers','pa')
            ->groupBy('pa.produit_id')
            ->orderBy('pa.id','ASC');
        return $queryBuilder->execute()->fetchAll();
    }



    public function getPanier($id){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('pa.id','pa.quantite','pa.prix','pa.dateAjoutPanier','pa.user_id','pa.produit_id','pa.commande_id')
            ->from('paniers','pa')
            ->where('pa.id='.$id);
        return $queryBuilder->execute()->fetch();
    }

    public function addPanier($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'prix'=>'?',
                'quantite'=>'quantite+1',
                'dateAjoutPanier'=>'?',
                'user_id'=>'?',
                'produit_id'=>'?',
                'commande_id'=>'?'
            ])
            ->setParameter(0,$donnees['prix'])
            ->setParameter(1,$donnees['dateAjoutPanier'])
            ->setParameter(2,$donnees['user_id'])
            ->setParameter(3,$donnees['id'])
            ->setParameter(4,$donnees['commande_id']);
        return $queryBuilder->execute();
    }
}