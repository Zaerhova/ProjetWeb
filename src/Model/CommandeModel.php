<?php
/**
 * Created by PhpStorm.
 * User: Ciryion
 * Date: 12/11/2017
 * Time: 14:29
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandeModel {

    private $db;

    function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllCommande()
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('c.id','c.user_id','c.prix','c.date_achat','c.etat_id','e.libelle')
            ->from('commandes','c')
            ->innerJoin('c','etats','e','c.etat_id = e.id');
        return $queryBuilder->execute()->fetchAll();
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

    public function createCommandeTransat($user_id){
        $conn=$this->db;
        $conn->beginTransaction();
        $requestSQL=$conn->prepare('select sum(prix*quantite) as prix from paniers where user_id = :idUser and commande_id is Null');
        $requestSQL->execute(['idUser'=>$user_id]);
        $conn->commit();
        $prix = $requestSQL->fetch()['prix'];
        $conn->beginTransaction();
        $requestSQL=$conn->prepare('insert into commandes(user_id,etat_id,prix) value (?,?,?)');
        $requestSQL->execute([$user_id,1,$prix]);
        $lastinsertid = $conn->lastInsertId();
        $requestSQL=$conn->prepare('update paniers set commande_id = ? where user_id=? and commande_id is null');
        $requestSQL->execute([$lastinsertid, $user_id]);
        $conn->commit();
    }

    public function getCommandeUser($user_id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('c.id','c.user_id','c.prix','c.date_achat','c.etat_id','e.libelle')
            ->from('commandes','c')
            ->innerJoin('c','etats','e','c.etat_id = e.id')
            ->where('user_id=:idUser')
            ->setParameter('idUser',(int)$user_id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function getCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('c.id','c.user_id','c.prix','c.date_achat','c.etat_id','e.libelle')
            ->from('commandes','c')
            ->innerJoin('c','etats','e','c.etat_id = e.id')
            ->where('c.id=:id')
            ->setParameter('id',(int)$id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function getProduitCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('pr.nom','pr.prix','pr.photo')
            ->from('commandes','c')
            ->innerJoin('c','paniers','p','p.commande_id = c.id')
            ->innerJoin('p','produits','pr','pr.id = p.produit_id')
            ->where('c.id=:id')
            ->setParameter('id',(int)$id);
        return $queryBuilder->execute()->fetchAll();

    }

    public function updateCommande($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('commandes')
            ->set('etat_id','2')
            ->where('id='.$id);
        return $queryBuilder->execute();

    }

}