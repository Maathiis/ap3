<?php
namespace models;

use models\base\SQL;

class RessourceModel extends SQL
{
    public function __construct()
    {
        parent::__construct('ressource', 'idressource');
    }

    public function getAll(): array
    {
        $sql = 'SELECT * FROM ressource LEFT JOIN categorie ON categorie.idcategorie = ressource.idcategorie';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getRandomRessource($limit = 3)
    {
        $sql = 'SELECT * FROM ressource LEFT JOIN categorie ON categorie.idcategorie = ressource.idcategorie  ORDER BY RAND() LIMIT ?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getRandomRessourceByVille($idVilles ,$limit = 3) 
    {
        $sql = 'SELECT ressource.* FROM ressource JOIN exemplaire ON exemplaire.idressource = ressource.idressource JOIN villes ON villes.idVilles = exemplaire.idVilles WHERE villes.idVilles = ? ORDER BY RAND() LIMIT ?';
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute([$idVilles, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getNewRessource($limit = 5)
    {
        $sql = 'SELECT DISTINCT r.*,c.* FROM ressource r LEFT JOIN exemplaire e ON r.idressource = e.idressource LEFT JOIN categorie c ON c.idcategorie = r.idcategorie ORDER BY e.dateentree DESC LIMIT ?';
        $stmt = parent::getPdo() ->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);

    }

    public function getNewRessourceByVille($limit = 5)
    {
        $sql = 'SELECT DISTINCT r.*,c.* FROM ressource r LEFT JOIN exemplaire e ON r.idressource = e.idressource LEFT JOIN villes v ON v.idVilles = e.idVilles WHERE v.idVilles = ? LEFT JOIN categorie c ON c.idcategorie = r.idcategorie ORDER BY e.dateentree DESC LIMIT ?';
        $stmt = parent::getPdo() ->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);

    } 

    public function getRessourceFilter($tabOfCategorie) {

        $endOfSentence ="";

        foreach ($tabOfCategorie as $categorie) {
            $endOfSentence .= " categorie.idcategorie = " . $categorie . " OR";
        }

        $sql = 'SELECT * FROM ressource INNER JOIN categorie ON categorie.idcategorie = ressource.idcategorie WHERE'. $endOfSentence;
        $sql = substr($sql, 0, strlen($sql)-2);
        $stmt = parent::getPdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function countRessourceById($idEmprunteur) {
        $sql = 'SELECT idEmprunteur, COUNT(idRessource) AS nb_ressources_empruntees FROM emprunter WHERE idEmprunteur = ? GROUP BY idEmprunteur';
        $stmt = parent::getPdo() ->prepare($sql);
        $stmt->execute([$idEmprunteur]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);

    }
}