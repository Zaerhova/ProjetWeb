<?php
namespace App\Controller;

use App\Model\ProfileModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;   // modif version 2.0


class ProfileController implements ControllerProviderInterface
{
    private $profileModel;
    public function index(Application $app)
    {
        return $app->redirect($app["url_generator"]->generate("profile.showProfile"));
    }

    public function showProfile(Application $app){
        $this->profileModel = new ProfileModel($app);
        $userId = $app['session']->get('user_id');
        $user = $this->profileModel->getUser($userId);
        return $app['twig']->render("frontOff/mesCoordonnees.html.twig",['user'=>$user]);
    }

    public function updateProfile(Application $app){
        $this->profileModel = new ProfileModel($app);
        $user = $this->profileModel->getUser($app['session']->get('user_id'));
        return $app['twig']->render("frontOff/v_form_update_profile.html.twig",['user'=>$user]);
    }

    public function validFormUpdate(Application $app, Request $req){
        $donnees = [
            'nom' => htmlspecialchars($_POST['nom']),
            'adresse' => htmlspecialchars($_POST['adresse']),
            'ville' => htmlspecialchars($_POST['ville']),
            'code_postal' => htmlspecialchars($_POST['code_postal']),
            'id' => $_POST['id'],
        ];
        if ($donnees['nom'] != ""){
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';

        }
        if ($donnees['ville'] != ""){
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='ville composé de 2 lettres minimum';
        }

        if ($donnees['adresse'] != "") {
            if ((! preg_match("/^[0-9]*[A-Za-z ]{2,}/",$donnees['adresse']))) $erreurs['adresse']='adresse composé de 2 lettres minimum';

        }
        if ($donnees['code_postal'] != "") {
            if ((! preg_match("/[0-9]{5,}/",$donnees['code_postal']))) $erreurs['code_postal']='code postal faux (ex: 90000)';
        }
        if (empty($erreurs)){
            $this->profileModel = new ProfileModel($app);
            $this->profileModel->updateProfile($donnees);
            return $app->redirect($app["url_generator"]->generate('profile.index'));
        }
        else return $app["twig"]->render('frontOff/v_form_update_profile.html.twig',['user'=> $donnees,'erreurs'=>$erreurs]);
    }

    public function updatePseudo(Application $app){
        $this->profileModel = new ProfileModel($app);
        $user = $this->profileModel->getUser($app['session']->get('user_id'));
        return $app['twig']->render("frontOff/v_form_update_pseudo.html.twig",["user" => $user]);
    }

    public function validFormPseudo(Application $app){
        $donnees = [
            'username' => htmlspecialchars($_POST['pseudo']),
            'id' => $_POST['id'],
        ];
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['username']))) $erreurs['pseudo']='pseudo composé de 2 lettres minimum';
        if(empty($erreurs)){
            $this->profileModel = new ProfileModel($app);
            $this->profileModel->updatePseudo($donnees);
            return $app->redirect($app["url_generator"]->generate('profile.index'));

        }
        else return $app['twig']->render("frontOff/v_form_update_pseudo.html.twig",['user' => $donnees , 'erreurs' => $erreurs]);
    }

    public function updateEmail(Application $app){
        $this->profileModel = new ProfileModel($app);
        $user = $this->profileModel->getUser($app['session']->get('user_id'));
        return $app['twig']->render("frontOff/v_form_update_email.html.twig",['user' => $user]);
    }

    public function validFormEmail(Application $app){
        $donnees = [
            'email' => htmlspecialchars($_POST['email']),
            'id' => $_POST['id'],
        ];
        if (! preg_match("/[A-Za-z0-9]{2,}.(@).[A-Za-z0-9]{2,}.(fr|com|de)/",$donnees['email'])) $erreurs['email']='mail faux (exemple.exemple@exemple.fr ou com)';
        if(empty($erreurs)){
            $this->profileModel = new ProfileModel($app);
            $this->profileModel->updateEmail($donnees);
            return $app->redirect($app["url_generator"]->generate('profile.index'));

        }
        else return $app['twig']->render("frontOff/v_form_update_email.html.twig",['user' => $donnees , 'erreurs' => $erreurs]);
    }

    public function updatePassword(Application $app){
        $this->profileModel = new ProfileModel($app);
        $user = $this->profileModel->getUser($app['session']->get('user_id'));
        return $app['twig']->render("frontOff/v_form_update_password.html.twig",['user' => $user]);
    }

    public function validFormPassword(Application $app){
        $donnees = [
            'motdepasse' => htmlspecialchars($_POST['motdepasse']),
            'id' => $_POST['id'],
        ];
        if (! preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,16}$/",$donnees['motdepasse'])) $erreurs['motdepasse']='mdp faux (mini 8 caractère, 1 majusucle et 1 chiffre)';
        if(empty($erreurs)){
            $this->profileModel = new ProfileModel($app);
            $this->profileModel->updatePassword($donnees);
            return $app->redirect($app["url_generator"]->generate('profile.index'));
        }
        else return $app['twig']->render("frontOff/v_form_update_password.html.twig",['user' => $donnees , 'erreurs' => $erreurs]);
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->match("/", 'App\Controller\ProfileController::index')->bind('profile.index');
        $controllers->get("/profile", 'App\Controller\ProfileController::showProfile')->bind('profile.showProfile');
        $controllers->get("/updatePerso",'App\Controller\ProfileController::updateProfile')->bind('profile.updatePerso');
        $controllers->put("/update",'App\Controller\ProfileController::validFormUpdate')->bind('profile.validFormUpdate');
        $controllers->get("/updatePseudo",'App\Controller\ProfileController::updatePseudo')->bind('profile.updatePseudo');
        $controllers->put("/updatePseudo",'App\Controller\ProfileController::validFormPseudo')->bind('profile.validFormPseudo');
        $controllers->get("/updateEmail",'App\Controller\ProfileController::updateEmail')->bind('profile.updateEmail');
        $controllers->put("/updateEmail",'App\Controller\ProfileController::validFormEmail')->bind('profile.validFormEmail');
        $controllers->get("/updatePassword",'App\Controller\ProfileController::updatePassword')->bind('profile.updatePassword');
        $controllers->put("/updatePassword",'App\Controller\ProfileController::validFormPassword')->bind('profile.validFormPassword');

        return $controllers;
    }


}
