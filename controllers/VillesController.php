<?php

namespace controllers;

use controllers\base\WebController;
use models\VillesModel;
use utils\Template;

class VillesController extends WebController
{
    // On déclare les modèles utilisés par le contrôleur.

    private VillesModel $villes;

    function __construct()
    {
        $this->villes = new VillesModel();
    }

    function listeVilles() {
        return Template::render("views/global/villes.php" , []);
    }
    
    function homeVille($nomVille) {
        return Template::render("views/global/home.php" , ["nomVille" => $nomVille]);
    }
    
}
