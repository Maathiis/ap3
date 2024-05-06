<?php
namespace models\classes;

use models\CommentairesModel;

class Commentaires
{
    private string $idCom;
    private string $idEmprunteur;
    private string $idRessource;
    private string $commentaire;
    private string $date_commentaire;
    private string $titre_commentaire;
    private CommentairesModel $commentaireModele;

    function __construct()
    {
        $this->commentaireModele = new CommentairesModel();
    
    }

    /**
     * Retourne la liste des contacts du client
     * @return Contact[]
     */
    public function lesCommentaires(): array
    {
        return $this->commentaireModele->lesCommentairesClients($this->idCom);
    }

    /**
     * @return string
     */
    public function getIdCom(): string
    {
        return $this->idCom;
    }

    /**
     * @param string $idCom
     */
    public function setIdCom(string $idCom): void
    {
        $this->idCom = $idCom;
    }

    /**
     * @return string
     */
    public function getIdEmprunteur(): string
    {
        return $this->idEmprunteur;
    }

    /**
     * @param string $idEmprunteur
     */
    public function setIdEmprunteur(string $idEmprunteur): void
    {
        $this->idEmprunteur = $idEmprunteur;
    }

    /**
     * @return string
     */
    public function getIdRessource(): string
    {
        return $this->idRessource;
    }

    /**
     * @param string $idRessource
     */
    public function setIdRessource(string $idRessource): void
    {
        $this->idRessource = $idRessource;
    }

    /**
     * @return string
     */
    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    /**
     * @param string $commentaire
     */
    public function setCommentaire(string $commentaire): void
    {
        $this->commentaire = $commentaire;
    }

    /**
     * @return string
     */
    public function getDate_commentaire(): string
    {
        return $this->date_commentaire;
    }

    /**
     * @param string $date_commentaire
     */
    public function setDate_commentaire(string $date_commentaire): void
    {
        $this->date_commentaire = $date_commentaire;
    }

    /**
     * @return string
     */
    public function getTitre_commentaire(): string
    {
        return $this->titre_commentaire;
    }

    /**
     * @param string $titre_commentaire
     */
    public function setTitre_commentaire(string $titre_commentaire): void
    {
        $this->titre_commentaire = $titre_commentaire;
    }

}