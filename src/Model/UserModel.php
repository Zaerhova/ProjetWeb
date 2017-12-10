<?php
namespace App\Model;

use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;;

class UserModel {

	private $db;

	public function __construct(Application $app) {
		$this->db = $app['db'];
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT id,username,roles FROM users WHERE username = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}
	// public function verif_login_mdp_Utilisateur($login,$mdp){
	// 	$sql = "SELECT id,login,password,droit FROM users WHERE login = ? AND password = ?";
	// 	$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
	// 	if($res->rowCount()==1)
	// 		return $res->fetch();
	// 	else
	// 		return false;
	// }

	public function getUser($user_id) {
		$queryBuilder = new QueryBuilder($this->db);
		$queryBuilder
			->select('*')
			->from('users')
			->where('id = :idUser')
			->setParameter('idUser', $user_id);
		return $queryBuilder->execute()->fetch();

	}

    public function getLogins()
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('username')
            ->from('users');
        return $queryBuilder->execute()->fetchAll();
    }

    public function register($donnees)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('users')
            ->values([
                'username' => '?',
                'password' => '?',
                'roles' => '?',
                'email' => '?',
                'nom' => '?',
                'code_postal' => '?',
                'ville' => '?',
                'adresse' => '?'
            ])
            ->setParameter(0,$donnees['login'])
            ->setParameter(1,$donnees['motdepasse'])
            ->setParameter(2,'ROLE_CLIENT')
            ->setParameter(3,$donnees['email'])
            ->setParameter(4,$donnees['nom'])
            ->setParameter(5,$donnees['codePostal'])
            ->setParameter(6,$donnees['ville'])
            ->setParameter(7,$donnees['adresse']);
        $queryBuilder->execute();
    }
}