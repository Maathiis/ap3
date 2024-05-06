<?php

namespace models;

use models\base\SQL;
use models\classes\Commentaires;


class CommentairesModel extends SQL
{
    public function __construct()
    {
        parent::__construct('commentaires', 'idCom');
    }
    

    public function lesCommentairesClients() : array
    {
        $query = "SELECT * FROM commentaires";
        $stmt = SQL::getPdo()->prepare($query);
        $stmt->execute([]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Commentaires::class);

    }

    public function ajoutCommentaire(mixed $idEmprunteur, mixed $idRessource, mixed $commentaire)
    {

        try {
        
            $sql = 'INSERT INTO commentaires (idEmprunteur, idRessource, commentaire, date_commentaire) VALUES (?, ?, ?, NOW())';
            $stmt = parent::getPdo()->prepare($sql);
            $result = $stmt->execute([$idEmprunteur, $idRessource, $commentaire]);
            return $result;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

}