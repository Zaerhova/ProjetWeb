<?php
/**
 * Created by PhpStorm.
 * User: Ciryion
 * Date: 12/11/2017
 * Time: 14:29
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use function MongoDB\BSON\fromJSON;
use Silex\Application;

class CommandeModel {

    private $db;

    function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getCommande($donnees){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id as commande_id','c.user_id','c.prix','c.date_achat','c.etat_id')
            ->from('commandes','c')
            ->where('c.user_id='.$donnees['user_id']);
        return $queryBuilder->execute()->fetch();
    }

    public function addCommande($donnees){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commandes')
            ->values([
                'user_id'=>'?',
                'prix'=>'?',
                'date_achat'=>'?',
                'etat_id'=>'?'
            ])
            ->setParameter(0,$donnees['user_id'])
            ->setParameter(1,$donnees['prix'])
            ->setParameter(2,$donnees['dateAjoutPanier'])
            ->setParameter(3,$donnees['etat_id']);
        return $queryBuilder->execute();

    }

}