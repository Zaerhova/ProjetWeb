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

    public function createCommandeTransat($user_id, $prix_total){
        $conn=$this->db;
        $conn->beginTransiction();
        $requestSQL=$conn->prepare('select sum(prix*quantite) as prix from paniers where user_id = :idUser and commande_id is Null');
        $requestSQL->execute(['idUser'=>$user_id]);
        $prix = $requestSQL->fetch()['prix'];
        $conn->commit();
        $conn->beginTransaction();
        $requestSQL=$conn->prepare('insert into commandes(user_id,etat_id) value (?,?,?)');
        $requestSQL->execute([$user_id,$prix,1]);
        $lastinsertid = $conn->lastInsertId();
        $requestSQL=$conn->prepare('update paniers set comannde_id = ? where user_id=? and commande_id is null');
        $requestSQL->execute([$lastinsertid, $user_id]);
        $conn->commit();
    }

}