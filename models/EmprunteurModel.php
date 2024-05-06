<?php

namespace models;

use models\base\SQL;
use utils\EmailUtils;
use utils\SessionHelpers;
use utils\TokenHelpers;

class EmprunteurModel extends SQL
{
    public function __construct()
    {
        parent::__construct('emprunteur', 'idemprunteur');
    }

    public function getUserbyId(mixed $idEmprunteur) {
        $sql = 'SELECT * FROM emprunteur WHERE idemprunteur = ?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$idEmprunteur]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function connexion(mixed $email, mixed $password)
    {
        /**
         * Rappel
         *
         * La validation du compte est un int qui prend plusieurs valeurs :
         * 0 : Compte non validé
         * 1 : email validé
         * 2 : Compte validé par un admin
         * 3 : Compte banni
         * 4 : Compte supprimé
         */

        // TODO Il ne faut pas autoriser la connexion si le compte n'est pas validé

        $sql = 'SELECT * FROM emprunteur WHERE emailemprunteur = ?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_OBJ);

        if ($user == null) {
            return false;
        }

        if (password_verify($password, $user->motpasseemprunteur)) {
            SessionHelpers::login($user);
            return true;
        }

        return false;
    }

    public function verifEtat(string $email)
    {

        $sql = 'SELECT etat FROM emprunteur WHERE emailemprunteur = ?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$email]);
        $etat = $stmt->fetch(\PDO::FETCH_COLUMN);

        return $etat;
    }

    public function creerEmprunteur(mixed $genre, mixed $email, mixed $tel, mixed $password, mixed $nom, mixed $prenom): bool
    {
        // Création du hash du mot de passe (pour le stockage en base de données)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $config = include("configs.php");

        try {
            // Création de l'utilisateur en base de données.

            // La validation du compte est un int qui prend plusieurs valeurs :
            // 0 : Compte non validé
            // 1 : email validé
            // 2 : Compte validé par un admin
            // 3 : Compte banni
            // 4 : Compte supprimé

            $UUID = TokenHelpers::guidv4(); // Génération d'un UUID v4, qui sera utilisé pour la validation du compte
            $sql = 'INSERT INTO emprunteur (genre, emailemprunteur, telportable, motpasseemprunteur, nomemprunteur, prenomemprunteur, datenaissance, validationcompte, validationtoken) VALUES (?,?, ?, ?, ?, ?, NOW(), 0, ?)';
            $stmt = parent::getPdo()->prepare($sql);
            $result = $stmt->execute([$genre, $email, $tel, $password_hash, $nom, $prenom, $UUID]);

            if ($result) {
                // Envoi d'un email de validation du compte
                // On utilise la fonction sendEmail de la classe EmailUtils
                // L'email contient un lien vers la page de validation du compte, avec l'UUID généré précédemment
                EmailUtils::sendEmail(
                    $email,
                    "Bienvenue $nom",
                    "newAccount",
                    array(
                        "url" => $config["URL_VALIDATION"] . $UUID,
                        "email" => $email,
                    )
                );
            }

            return true;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function modifierEmprunteur(mixed $genre, mixed $nom, mixed $prenom, mixed $email, mixed $tel, mixed $idEmprunteur): bool
    {

        try {
            // Exécutez une instruction SQL UPDATE pour mettre à jour les informations
            $sql = 'UPDATE emprunteur SET genre = ?, nomemprunteur = ?, prenomemprunteur = ?, emailemprunteur = ?, telportable = ?  WHERE idemprunteur = ?';
            $stmt = parent::getPdo()->prepare($sql);
            $result = $stmt->execute([$genre, $nom, $prenom, $email, $tel,  $idEmprunteur]);

            if ($result) {
                // Mise à jour réussie, vous pouvez effectuer d'autres actions si nécessaire
                SessionHelpers::login($this->getUserbyId($idEmprunteur));
                return true;
            } else {
                // Échec de la mise à jour
                return false;
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function modifierMDPmodel (mixed $oldMDP, mixed $newMDP, mixed $confirmMDP, mixed $idemprunteur) 
    {
        $user = SessionHelpers::getConnected();
        if (password_verify($oldMDP, $user->motpasseemprunteur)) {
            if ($newMDP == $confirmMDP) {

                $sql = 'UPDATE emprunteur SET motpasseemprunteur = ? WHERE idemprunteur = ?';
                $stmt = parent::getPdo()->prepare($sql);
                $result = $stmt->execute([password_hash($newMDP, PASSWORD_DEFAULT), $idemprunteur]);
                return $result;
            }
        }
    }

    /**
     * Récupère tous les utilisateurs sans leur mot de passe.
     * @return array
     */
    public function getAllWithoutPassword(): array
    {
        $all = parent::getAll();

        // Ici, on utilise la fonction array_map qui permet d'appliquer une fonction sur tous les éléments d'un tableau
        // L'autre solution est d'utiliser une boucle foreach ou via une requête SQL avec un SELECT qui ne récupère pas le mot de passe
        return array_map(function ($user) {
            // On supprime le mot de passe de l'utilisateur
            unset($user->motpasseemprunteur);

            // On retourne l'utilisateur
            return $user;
        }, $all);
    }

    public function getHistorique($idemprunteur) {
        $sql = 'SELECT ressource.titre as Titre, libellecategorie as Categorie, datedebutemprunt as Emprunt, dateretour as Retour, EST_RENDU as Rendu FROM emprunter inner join ressource on emprunter.idressource=ressource.idressource inner join categorie on categorie.idcategorie=ressource.idcategorie where emprunter.idemprunteur=?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$idemprunteur]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function validateAccount($uuid)
    {
        $sql = 'UPDATE emprunteur SET validationtoken = NULL, etat = 1 WHERE validationtoken = ?';
        $stmt = parent::getPdo()->prepare($sql);
        $result = $stmt->execute([$uuid]);

        return $result;
    }
}
