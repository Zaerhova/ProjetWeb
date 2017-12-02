<?php
/**
 * Created by PhpStorm.
 * User: Ciryion
 * Date: 02/12/2017
 * Time: 18:30
 */

namespace App\Model;
use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ProfileModel
{

    private $db;

    function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getUser($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('*')
            ->from('users')
            ->where('id=:id')
            ->setParameter('id',$id);
        return $queryBuilder->execute()->fetch();
    }

    public function updateProfile($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('users')
            ->set('nom','?')
            ->set('adresse','?')
            ->set('ville','?')
            ->set('code_postal','?')
            ->where('id = ?')
            ->setParameter(0,$donnees['nom'])
            ->setParameter(1,$donnees['adresse'])
            ->setParameter(2,$donnees['ville'])
            ->setParameter(3,$donnees['code_postal'])
            ->setParameter(4,$donnees['id']);
        return $queryBuilder->execute();
    }
    public function updatePseudo($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('users')
            ->set('username','?')
            ->where('id = ?')
            ->setParameter(0,$donnees['username'])
            ->setParameter(1,$donnees['id']);
        return $queryBuilder->execute();
    }

    public function updateEmail($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('users')
            ->set('email','?')
            ->where('id = ?')
            ->setParameter(0,$donnees['email'])
            ->setParameter(1,$donnees['id']);
        return $queryBuilder->execute();
    }

    public function updatePassword($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('users')
            ->set('motdepasse','?')
            ->where('id = ?')
            ->setParameter(0,$donnees['motdepasse'])
            ->setParameter(1,$donnees['id']);
        return $queryBuilder->execute();
    }


}