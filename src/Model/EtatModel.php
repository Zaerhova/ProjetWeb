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

class EtatModel {

    private $db;

    public function getEtats(){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('e.id','e.libelle')
            ->from('etats','e');
        return $queryBuilder->execute()->fetchAll();
    }

    public function addCommande($donnees){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commandes')
            ->values([
                'user_id'=>'?',
                'prix'=>'',
                'date_achat'=>'?',
                'etat_id'=>'?'
            ])
            ->setParameter(0,$donnees['user_id'])
            ->setParameter(1,$donnees['prix'])
            ->setParameter(2,$donnees['date_achat'])
            ->setParameter(3,$donnees['etat_id']);
        return $queryBuilder->execute();

    }

}