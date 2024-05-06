<?php

namespace routes;

use controllers\ApiDocController;
use controllers\CatalogueController;
use controllers\MainController;
use controllers\UserController;
use controllers\VillesController;
use routes\base\Route;
use utils\SessionHelpers;
use utils\Template;

class Web
{
    function __construct()
    {
        $main = new MainController();
        $apidoc = new ApiDocController();
        $user = new UserController();
        $catalogue = new CatalogueController();
        $villes = new VillesController();

        // Appel la méthode « home » dans le contrôleur $main.
        Route::Add('/', [$main, 'home']);
        Route::Add('/exemple', [$main, 'exemple']);
        Route::Add('/exemple2/{parametre}', [$main, 'exemple']);



        // Appel la fonction inline dans le routeur.
        // Utile pour du code très simple, où un test, l'utilisation d'un contrôleur est préférable.
        // Si le code accède à la base de données, la création d'un contrôleur est requis.
        Route::Add('/horaires', fn() => Template::render('views/global/horaires.php'));
        Route::Add('/apropos', fn() => Template::render('views/global/apropos.php'));

        

        // Routes permettant la gestion de l'authentification.
        Route::Add('/login', [$user, 'login']);
        Route::Add('/signup', [$user, 'signup']);

        // Validation de l'inscription.
        Route::Add('/valider-compte/{uuid}', [$user, 'signupValidate']);

        if (SessionHelpers::isLogin()) {
            // Page de profil utilisateur.
            Route::Add('/me', [$user, 'me']);

            // Action de modification.
            Route::Add('/modifier', [$user, 'modifier']);
            Route::Add('/modifierMDP', [$user, 'modifierMDP']);

            // Action de déconnexion.
            Route::Add('/logout', [$user, 'logout']);

            // Action d'emprunt d'une ressource.
            Route::Add('/catalogue/emprunter', [$user, 'emprunter']);
            Route::Add('/me/telecharger', [$user, 'telechargerData']);

            // Action d'envoie de commentaire
            Route::Add('/sendcom/{id}', [$catalogue, 'envoieCommentaire']);

            // Action de rendre un emprunt
            Route::Add('/rendre/{idressource}/{idexemplaire}', [$catalogue, 'rendre']);

            Route::Add('/btnEmprunter',[$catalogue, 'btnEmprunter']);
           

        }

        $emprunteur = SessionHelpers::getConnected();
        if (SessionHelpers::isLogin() && $emprunteur->developpeur == 1) {
            // Routes permettant l'accès à la documentation de l'API uniquement si l'emprunteur est développeur.
            Route::Add('/api', [$apidoc, 'liste']);

        }

        // Route permettant l'accès au catalogue.
        Route::Add('/catalogue/detail/{id}', [$catalogue, 'detail']);
        Route::Add('/catalogue/{categorie}', [$catalogue, 'liste']);

        // Route permettant l'accès au choix des villes.
        Route::Add('/global/villes', [$villes, 'listeVilles']);

        // Route permettant l'accès à la médiathèque d'une ville
        Route::Add('/home/{nomVille}', [$villes,'homeVille']);
    }
}

