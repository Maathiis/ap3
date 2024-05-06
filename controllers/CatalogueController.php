<?php

namespace controllers;

use controllers\base\WebController;
use models\EmprunterModel;
use models\ExemplaireModel;
use models\RessourceModel;
use models\CommentairesModel;
use models\CategorieModel;
use utils\SessionHelpers;
use utils\Template;


class CatalogueController extends WebController
{

    private RessourceModel $ressourceModel;
    private ExemplaireModel $exemplaireModel;
    private EmprunterModel $emprunterModel;
    private CategorieModel $categorieModel;
    private CommentairesModel $commentairesModele;

    function __construct()
    {
        $this->ressourceModel = new RessourceModel();
        $this->exemplaireModel = new ExemplaireModel();
        $this->emprunterModel = new EmprunterModel();
        $this->categorieModel = new CategorieModel();
        $this->commentairesModele  = new CommentairesModel();
    }

    /**
     * Affiche la liste des ressources.
     * @param string $type
     * @return string
     */

    function liste(string $categorie): string
    {
        $categories = $this->categorieModel->getALL();

        if ($categorie == "all") {

            $catalogue = $this->ressourceModel->getALL();

            return Template::render("views/catalogue/liste.php", array("titre" => "Ensemble du catalogue", "catalogue" => $catalogue, "categories" => $categories));
        }

        else if ($categorie == "tri" && isset($_GET['categories']))
        {
            $catalogue = $this->ressourceModel->getRessourceFilter($_GET['categories']);
            
            return Template::render("views/catalogue/liste.php", array("titre" => "Ensemble du catalogue", "catalogue" => $catalogue, "categories" => $categories));
        }
        else 
        {
            $catalogue = $this->ressourceModel->getAll();
            return Template::render("views/catalogue/liste.php", array("titre" => "Ensemble du catalogue", "catalogue" => $catalogue, "categories" => $categories));
        }
    }

    function rendre (int $idressource, int $idexemplaire) {
        $user = SessionHelpers::getConnected();
        $this->emprunterModel->rendreEmprunt($idressource, $user->idemprunteur, $idexemplaire);
        $this->emprunterModel->majExemplaireRendre($idressource, $idexemplaire);
        $this->redirect("/me");
        exit();
    }

    function btnEmprunter(): string
    {
        // Récupération de l'utilisateur connecté en SESSION.
        // La variable contient les informations de l'utilisateur présent en base de données.
        $user = SessionHelpers::getConnected();

        // Récupération des emprunts de l'utilisateur
        $emprunts = $this->emprunterModel->getEmprunts($user->idemprunteur);

        return Template::render("views/catalogue/detail.php", array("user" => $user, "emprunts" => $emprunts));
    }

    /**
     * Affiche le détail d'une ressource.
     * @param int $id
     * @return string
     */
    function detail(int $id): string
{
    $showEmprunter = false;
    $getNbEmprunt = null;
    // Récupération de la ressource
    $user = SessionHelpers::getConnected();
    $nbExemplaireDispo = $this->emprunterModel->nbExemplaireDispo($id);
    if ($user) {
        $getNbEmprunt = $this->emprunterModel->getNbEmprunt($user->idemprunteur);
        $verifRessourceRendu = $this->emprunterModel->verifRessourceRendu($user->idemprunteur, $id);
        
        
        if ($getNbEmprunt < 3 && $verifRessourceRendu == true && $nbExemplaireDispo > 0 ) { 
            $showEmprunter = true;
        } else {
            $showEmprunter = false;
        }
    }
    
    $ressource = $this->ressourceModel->getOne($id);
    $commentaires = $this->commentairesModele->lesCommentairesClients();
    $emprunter = null; // Initialisation à null

    if (SessionHelpers::isLogin()) {
        // Si l'utilisateur est connecté, récupérer les informations d'emprunt
        $emprunter = $this->emprunterModel->getEmpruntById($user->idemprunteur, $ressource->idressource);
    }

    if ($ressource == null) {
        $this->redirect("/");
    }

    // Récupération des exemplaires de la ressource
    $exemplaires = $this->exemplaireModel->getByRessource($id);
    $exemplaire = null;

    // Pour l'instant, on ne gère qu'un exemplaire par ressource.
    // Si on en trouve plusieurs, on prend le premier.
    if ($exemplaires && sizeof($exemplaires) > 0) {
        $exemplaire = $exemplaires[0];
    }

    $data = array("getNbEmprunt"=> $getNbEmprunt, "nbExemplaireDispo" => $nbExemplaireDispo ,"showEmprunter" => $showEmprunter, "ressource" => $ressource, "exemplaire" => $exemplaire, "user" => SessionHelpers::getConnected(), "commentaires" => $commentaires);

    // Si l'utilisateur est connecté, ajouter $emprunter aux données retournées
    if ($emprunter !== null) {
        $data["emprunter"] = $emprunter;
    }

    return Template::render("views/catalogue/detail.php", $data);
}

    function envoieCommentaire(int $id) {

        if (isset($_POST["idEmprunteur"]) && isset($_POST["idRessource"]) && isset($_POST["commentaire"])) {
            $commentaire = $this->commentairesModele->ajoutCommentaire($_POST["idEmprunteur"], $_POST["idRessource"], $_POST["commentaire"]);
            if ($commentaire) {
                //return Template::render("views/catalogue/detail.php");
                $this->redirect("/catalogue/detail/$id");
            } else {
                die("Erreur lors de l'ajout du commentaire");
            }
        }
    }
}