<?php

namespace models;

use models\base\SQL;

class EmprunterModel extends SQL
{
    public function __construct()
    {
        parent::__construct('emprunter', 'idemprunter');
    }

    /**
     * Déclare un emprunt dans la base de données.
     * @param $idRessource identifiant de la ressource empruntée (idressource)
     * @param $idExemplaire identifiant de l'exemplaire emprunté (idexemplaire)
     * @param $idemprunteur identifiant de l'emprunteur (lecteur)
     * @return bool true si l'emprunt a été déclaré, false sinon.
     */
    public function declarerEmprunt($idRessource, $idExemplaire, $idemprunteur): bool
    {
        try {
            $sql = 'INSERT INTO emprunter (idressource, idexemplaire, idemprunteur, datedebutemprunt, dureeemprunt, dateretour, EST_RENDU) VALUES (?, ?, ?, NOW(), 30, DATE_ADD(NOW(), INTERVAL 1 MONTH), 0)';
            $stmt = parent::getPdo()->prepare($sql);
            return $stmt->execute([$idRessource, $idExemplaire, $idemprunteur]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function exemplaireDispo($idRessource) 
    {
        try {
            $sql = 'SELECT idExemplaire FROM exemplaire WHERE idRessource = ? AND EST_DISPO = 1 LIMIT 1';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idRessource]);
            $idExemplaire = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $idExemplaire;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function majExemplaireEmprunt($idRessource) 
    {
        try {
            $sql = 'UPDATE exemplaire SET EST_DISPO = 0 WHERE idRessource = ? AND EST_DISPO = 1 LIMIT 1';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idRessource]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function majExemplaireRendre($idRessource, $idexemplaire) 
    {
        try {
            $sql = 'UPDATE exemplaire SET EST_DISPO = 1 WHERE idRessource = ? AND EST_DISPO = 0 AND idExemplaire = ? LIMIT 1';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idRessource, $idexemplaire]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function emprunt($idRessource, $idExemplaire, $idemprunteur){
        try {
            $sql = 'SELECT * FROM emprunter e JOIN ressource r ON e.idressource = r.idressource WHERE idemprunteur = ? AND e.idressource = ? AND idexemplaire = ? ORDER BY datedebutemprunt DESC LIMIT 1';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idemprunteur, $idRessource, $idExemplaire]);
            return $stmt->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return false;
        }
    }
    

    /**
     * Récupère les emprunts d'un emprunteur en fonction de son id (idemprunteur)
     * @param $idemprunteur
     * @return bool
     */
    public function getEmprunts($idemprunteur): bool|array
    {
        try {
            $sql = 'SELECT * FROM emprunter LEFT JOIN ressource ON emprunter.idressource = ressource.idressource LEFT JOIN categorie ON categorie.idcategorie = ressource.idcategorie WHERE idemprunteur = ?';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idemprunteur]);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getEmpruntById($idemprunteur, $id)
    {
        try {
            $sql = 'SELECT * FROM emprunter LEFT JOIN ressource ON emprunter.idressource = ressource.idressource LEFT JOIN categorie ON categorie.idcategorie = ressource.idcategorie WHERE idemprunteur = ? AND emprunter.idressource = ?';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idemprunteur, $id]);
            return $stmt->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Retourne les 5 ressources les plus empruntées.
     * @return array|false
     */
    public function getTopEmprunts(): array
    {
        try {
            $sql = 'SELECT COUNT(emprunter.idressource) AS nbEmprunt, titre, emprunter.idressource FROM emprunter LEFT JOIN ressource ON emprunter.idressource = ressource.idressource GROUP BY emprunter.idressource ORDER BY nbEmprunt DESC LIMIT 5';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function rendreEmprunt($idressource, $idemprunteur, $idexemplaire) 
    {
        try {
            $sql = 'UPDATE emprunter SET EST_RENDU = 1, dateretour = NOW() WHERE idressource = ? AND idemprunteur = ? AND idexemplaire = ?';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idressource, $idemprunteur, $idexemplaire]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // ----------------------------------------------------------- Fonction pour afficher le bouton emprunter --------------------------------------------------------------
    public function getNbEmprunt($idemprunteur) {
        try {
            $sql = 'SELECT COUNT(CASE WHEN EST_RENDU = 0 THEN 1 END) AS nb_zeros FROM emprunter WHERE idemprunteur = ?';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idemprunteur]);
            return $stmt->fetchColumn(); // Retourne le nbre de d'emprunt de l'utilisateur
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function verifRessourceRendu($idemprunteur, $idressource) {
        try {
            $sql = 'SELECT * FROM emprunter WHERE idemprunteur = ? AND idressource = ? AND EST_RENDU = 0';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idemprunteur, $idressource]);
    
            // Check if there is a matching record with EST_RENDU = 0
            if ($stmt->rowCount() > 0) {
                return false; // Found a record with EST_RENDU = 0
            } else {
                return true;  // No matching record found
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return true; // Exception occurred
        }
    }

    public function nbExemplaireDispo($idressource) {
        try {
            $sql = 'SELECT COUNT(*) AS nb_exemplaires_disponibles FROM exemplaire WHERE idressource = ? AND EST_DISPO = 1';
            $stmt = parent::getPdo()->prepare($sql);
            $stmt->execute([$idressource]);
            return $stmt->fetchColumn(); // Retourne le nbre de d'exemplaire dispo pour la ressource sélectionné
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false; 
        }
    }
    // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------
}




