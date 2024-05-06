<?php

namespace controllers;

use controllers\base\WebController;
use models\EmprunterModel;
use models\EmprunteurModel;
use models\RessourceModel;
use utils\SessionHelpers;
use utils\Template;
use utils\EmailUtils;

class UserController extends WebController
{
    // On déclare les modèles utilisés par le contrôleur.
    private EmprunteurModel $emprunteur; // Modèle permettant d'interagir avec la table emprunteur
    private EmprunterModel $emprunter; // Modèle permettant l'emprunt
    private RessourceModel $ressourceModel;

    function __construct()
    {
        $this->emprunteur = new EmprunteurModel();
        $this->emprunter = new EmprunterModel();
        $this->ressourceModel = new RessourceModel();
    }

    /**
     * Déconnecte l'utilisateur.
     * @return string
     */
    function logout(): string
    {
        SessionHelpers::logout();
        return $this->redirect("/");
    }

    /**
     * Affiche la page de connexion.
     * Si l'utilisateur est déjà connecté, il est redirigé vers sa page de profil.
     * Si la connexion échoue, un message d'erreur est affiché.
     * @return string
     */
    function login(): string
    {
        $data = array();

        // Si l'utilisateur est déjà connecté, on le redirige vers sa page de profil
        if (SessionHelpers::isConnected()) {
            return $this->redirect("/me");
        }


        // Gestion de la connexion
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            $result = $this->emprunteur->connexion($_POST["email"], $_POST["password"]);
            $etat = $this->emprunteur->verifEtat($_POST["email"]);
            // Si la connexion est réussie, on redirige l'utilisateur vers sa page de profil
            if ($etat == 0) {
                $data["error"] = "Votre compte n'est pas activé, veuillez cliquer sur le lien envoyé par mail.";
            } else if ($etat == 3) {
                $data["error"] = "Votre compte est banni.";
            } else if ($etat == 4) {
                $data["error"] = "Le compte est supprimé. (Mais si il est supprimé il est pas censé être dans la bdd ??)";
            } else if ($result && $etat == 1 || $etat == 2) {
                $this->redirect("/me");
            } else {
                // Sinon, on affiche un message d'erreur
                $data["error"] = "Email ou mot de passe incorrect";
            }
        }

        // Affichage de la page de connexion
        return Template::render("views/user/login.php", $data);
    }

    function modifier(): string
    {
        if (isset($_POST["genre"]) && isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["email"]) && isset($_POST["tel"]) && isset($_POST["idemprunteur"])) {
            $result = $this->emprunteur->modifierEmprunteur($_POST["genre"], $_POST["nom"], $_POST["prenom"], $_POST["email"], $_POST["tel"],  $_POST["idemprunteur"]);
            
            if ($result) {
                return $this->redirect("/me");
            } else {
                $data["error"] = "La modification du compte a échoué";
                return Template::render("views/user/confirmation.php");
            }
        } else {
            return Template::render("views/user/modifier.php" , ["user" => SessionHelpers::getConnected()]);

        }
    }

    function modifierMDP(): string
    {
        $mess = "Super ! mdp changé";
        if (isset($_POST["oldMDP"], $_POST["newMDP"],$_POST["confirmMDP"],$_POST["idemprunteur"])) {
            //si même mot de passe mettre mess que tu veux & return template avec mess
            if($_POST["oldMDP"] == $_POST["newMDP"]) {
                $mess = "Vous ne pouvez pas mettre le même mot de passe !";
                return Template::render("views/user/modifier.php" , ["user" => SessionHelpers::getConnected(),"message" => $mess]);
            }
            $result = $this->emprunteur->modifierMDPmodel($_POST["oldMDP"], $_POST["newMDP"], $_POST["confirmMDP"], $_POST["idemprunteur"]);
            if (!$result) {
                $mess = "La modification du mot de passe a échoué";
            } 
        } 
        return Template::render("views/user/modifier.php" , ["user" => SessionHelpers::getConnected(),"message" => $mess]);
    }
    /**
     * Affiche la page d'inscription.
     * Si l'utilisateur est déjà connecté, il est redirigé vers sa page de profil.
     * Si l'inscription échoue, un message d'erreur est affiché.
     * @return string
     */
    function signup(): string
    {
        $data = array();

        // Si l'utilisateur est déjà connecté, on le redirige vers sa page de profil
        if (SessionHelpers::isConnected()) {
            return $this->redirect("/me");
        }


        // Gestion de l'inscription
        if (isset($_POST["genre"]) && isset($_POST["email"]) && isset($_POST["tel"]) && isset($_POST["password"]) && isset($_POST["nom"]) && isset($_POST["prenom"])) {


            // Récupération de la préférence de confidentialité du numéro de téléphone
            $afficher_numero = isset($_POST["afficher_numero"]) ? $_POST["afficher_numero"] : "non";

            $result = $this->emprunteur->creerEmprunteur($_POST["genre"], $_POST["email"], $_POST["tel"], $_POST["password"], $_POST["nom"], $_POST["prenom"], $_POST["afficher_numero"]);

            // Si l'inscription est réussie, on affiche un message de succès
            if ($result) {
                return Template::render("views/user/signup-success.php");
            } else {
                // Sinon, on affiche un message d'erreur
                $data["error"] = "La création du compte a échoué";
            }
        }

        // Affichage de la page d'inscription
        return Template::render("views/user/signup.php", $data);
    }

    function signupValidate($uuid)
    {
        $result = $this->emprunteur->validateAccount($uuid);

        if ($result) {
            // Affichage de la page de finalisation de l'inscription
            return Template::render("views/user/signup-validate-success.php");
        } else {
            // Gestion d'erreur à améliorer
            return parent::redirect("/");
        }
    }

    /**
     * Affiche la page de profil de l'utilisateur connecté.
     * Si l'utilisateur n'est pas connecté, il est redirigé vers la page de connexion.
     * @return string
     */
    function me(): string
    {
        // Récupération de l'utilisateur connecté en SESSION.
        // La variable contient les informations de l'utilisateur présent en base de données.
        $user = SessionHelpers::getConnected();

        $nbrRessourceEmprunte = $this->ressourceModel->countRessourceById($user->idemprunteur);
        // Récupération des emprunts de l'utilisateur
        $emprunts = $this->emprunter->getEmprunts($user->idemprunteur);

        return Template::render("views/user/me.php", array("user" => $user, "emprunts" => $emprunts, "nbrRessourceEmprunte" => $nbrRessourceEmprunte));
    }

    function telechargerData() {
        $user = SessionHelpers::getConnected();
        unset($user->motpasseemprunteur);
        $info=array(
            "user" => $user,
            "historique" => $this->emprunteur->getHistorique($user->idemprunteur),
        );

        header('Content-disposition:attachment; filename=donnee.json');
        header('Content-type: application/json');

        echo json_encode($info);
        return null;
    }
   


   
    /*function modifierLeMDP (): {
        
    }*/
    /**
     * Affiche la page de profil d'un utilisateur.
     * Si l'utilisateur n'est pas connecté, il est redirigé vers la page de connexion.
     * Pour accéder à la page il faut également l'id de la ressource et l'id de l'exemplaire.
     * @return string
     */
    function emprunter()
    {
        // Id à emprunter
        $idRessource = $_POST["idRessource"];

        // Récupération de l'utilisateur connecté en SESSION.
        $user = SessionHelpers::getConnected();

        if (!$user || !$idRessource) {
            // Gestion d'erreur à améliorer
            die("Erreur: utilisateur non connecté ou ids non renseignés");
        }
        // On déclare l'emprunt, et on redirige l'utilisateur vers sa page de profil
        $idExemplaire = $this->emprunter->exemplaireDispo($idRessource) ["idExemplaire"];
        if (!$idExemplaire) {
            die("Plus d'exemplaire pour la ressource demandé.");
        }
        $this->emprunter->majExemplaireEmprunt($idRessource);
        $result = $this->emprunter->declarerEmprunt($idRessource, $idExemplaire, $user->idemprunteur);
        if ($result) {
            $data = $this->emprunter->emprunt($idRessource, $idExemplaire, $user->idemprunteur);

            EmailUtils::sendEmail(
                $user->emailemprunteur,
                "Recapitulatif de votre emprunt",
                "emprunter",
                array(
                    "titreEmprunt" => $data->titre,
                    "debutEmprunt" => $data->datedebutemprunt,
                    "finEmprunt" => $data->dateretour
                )
            );
            $this->redirect("/me");
        } else {
            // Gestion d'erreur à améliorer
            die("Erreur lors de l'emprunt");
        }
    }

    function nbrRessourceEmprunte () {
        $user = SessionHelpers::getConnected();
        $nbrRessourceEmprunte = $this->ressourceModel->countRessourceById($user->idemprunteur);
        return Template::render("views/user/me.php", $nbrRessourceEmprunte);
    }
    
}
