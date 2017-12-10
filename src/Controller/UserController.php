<?php
namespace App\Controller;

use App\Model\UserModel;
use Gregwar\Captcha\CaptchaBuilder;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class UserController implements ControllerProviderInterface {

	private $userModel;

	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('login.html.twig');
	}

	public function validFormConnexionUser(Application $app, Request $req)
	{

		$app['session']->clear();
		$donnees['login']=$req->get('login');
		$donnees['password']=$req->get('password');

		$this->userModel = new UserModel($app);
		$data=$this->userModel->verif_login_mdp_Utilisateur($donnees['login'],md5($donnees['password']));

		if($data != NULL)
		{
			$app['session']->set('roles', $data['roles']);  //dans twig {{ app.session.get('roles') }}
			$app['session']->set('username', $data['username']);
			$app['session']->set('logged', 1);
			$app['session']->set('user_id', $data['id']);
			return $app->redirect($app["url_generator"]->generate("accueil"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('login.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("accueil"));
	}

	public function register(Application $app){
        $builder = new CaptchaBuilder;
        $builder->build();
        $_SESSION['phrase'] = $builder->getPhrase();
	    return $app['twig']->render('register.html.twig',['captcha'=>$builder->inline()]);
    }

    public function validFormRegister(Application $app){
	    $this->userModel = new UserModel($app);
	    $logins = $this->userModel->getLogins();
	    $err = false;
        $donnees = [
            'nom' => htmlspecialchars($_POST['nom']),
            'login' => htmlspecialchars($_POST['login']),
            'codePostal' => htmlspecialchars($_POST['codePostal']),
            'email' => htmlspecialchars($_POST['email']),
            'ville' => htmlspecialchars($_POST['ville']),
            'adresse' => htmlspecialchars($_POST['adresse']),
            'password' => htmlspecialchars($_POST['password']),
            'password2' => htmlspecialchars($_POST['password2']),
            'captcha' => htmlspecialchars($_POST['captcha'])
        ];
        if ($donnees['nom'] != ""){
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
        }
        if ($donnees['ville'] != ""){
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='ville composé de 2 lettres minimum';
        }
        if ($donnees['email'] != "") {
            if (!preg_match("/[A-Za-z0-9]{2,}.(@).[A-Za-z0-9]{2,}.(fr|com|de)/", $donnees['email'])) $erreurs['email'] = 'mail faux (exemple.exemple@exemple.fr ou com)';
        }
        if ($donnees['adresse'] != "") {
            if ((! preg_match("/^[0-9]*[A-Za-z ]{2,}/",$donnees['adresse']))) $erreurs['adresse']='adresse composé de 2 lettres minimum';

        }
        if ($donnees['codePostal'] != "") {
            if ((! preg_match("/[0-9]{5,}/",$donnees['codePostal']))) $erreurs['codePostal']='code postal faux (ex: 90000)';
        }

        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['login']))) $erreurs['login']='nom composé de 2 lettres minimum';
        foreach ($logins as $login) {
            if ($donnees['login'] == $login['username']){
                $err = true;
            }
        }if($err == true)$erreurs['login']='Login déja existant';
        if ($donnees['captcha'] != $_SESSION['phrase'])$erreurs['captcha']='Captcha incorrect';
        if (! preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,16}$/",$donnees['password'])) $erreurs['password']='mdp faux (mini 8 caractère, 1 majusucle et 1 chiffre)';
        if ($donnees['password'] != $donnees['password2'])$erreurs['password2'] ='mot de passes differents';
        if (empty($erreurs)){
            $donnees['motdepasse'] = md5($donnees['password']);
            $this->userModel = new UserModel($app);
            $this->userModel->register($donnees);
            return $app->redirect($app["url_generator"]->generate('user.login'));
        }else{
            $builder = new CaptchaBuilder;
            $builder->build();
            $_SESSION['phrase'] = $builder->getPhrase();
            return $app['twig']->render('register.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'captcha'=>$builder->inline()]);
        }


    }
	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		$controllers->match('/', 'App\Controller\UserController::index')->bind('user.index');
		$controllers->get('/login', 'App\Controller\UserController::connexionUser')->bind('user.login');
		$controllers->post('/login', 'App\Controller\UserController::validFormConnexionUser')->bind('user.validFormlogin');
		$controllers->get('/logout', 'App\Controller\UserController::deconnexionSession')->bind('user.logout');
		$controllers->get('/register', 'App\Controller\UserController::register')->bind('user.register');
		$controllers->post('/register', 'App\Controller\UserController::validFormRegister')->bind('user.validFormRegister');
		return $controllers;
	}
}