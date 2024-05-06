# Structure MVC

Cette structure est réalisée à des fins pédagogiques. Elle est un intermédiaire permettant d'introduire les concepts du
framework Laravel sur des bases de développement PHP connu.

- [Aide mémoire](https://cours.brosseau.ovh/cheatsheets/mini-mvc-sample/)
- [Document associé disponible ici](https://cours.brosseau.ovh/tp/php/mvc/tp1.html)
- [Exemple de code utilisant cette structure](https://github.com/c4software/demo-structure-mvc-video/)

---

## Usage

### Initialiser la base de données

```shell
php mvc db:migrate
```

### Créer un nouveau modèle

```shell
php mvc model:create NomDuModele
```

### Créer un nouveau controller

```shell
php mvc controller:create NomDuControler
```

### Lancer le projet

```shell
php mvc serve
```

## Déployer sur Apache ou Docker

- [Déployer sur Apache](https://cours.brosseau.ovh/tp/ops/mini-mvc-sample/deployer-mini-mvc-sample.html)
- [Déployer avec Docker](https://cours.brosseau.ovh/tp/ops/mini-mvc-sample/mini-mvc-sample-docker.html)

---

Ce projet est réalisé à des fins pédagogiques. [Document associé disponible ici](https://cours.brosseau.ovh/tp/php/mvc/tp1.html)

---

**Note importante**, cette architecture est à but pédagogique seulement, si vous souhaitez réaliser un développement MVC je vous conseille fortement de partir sur une solution type Laravel.

A faire : 

- Bloquer les commentaires à 1 par personne par ressources mais l'utilisateur peux le supprimé et en réécrire un
- Afficher le nom prénom de l'utilisateur et pas son id dans la section commentaire

- Stats : ? : nbre de comm en tout, nombre d'emprunts, puis spécifiquement, nbre de bd, livre etc
        (SELECT idEmprunteur, COUNT(idCom) AS nombre_commentaires FROM commentaires WHERE idEmprunteur = 15 GROUP BY idEmprunteur; ) -> Compte le nombre de commentaires en tout
        (SELECT idRessource, COUNT(idCom) AS nb_commentaires FROM commentaires WHERE idEmprunteur = 15 GROUP BY idRessource;) -> Compte le nombre de coms par ressource
        (SELECT idEmprunteur, COUNT(idRessource) AS nb_ressources_empruntees FROM emprunter WHERE idEmprunteur = 15 GROUP BY idEmprunteur;) -> Compte le nbre de ressources emprunté


- Faire en sorte qu'il ne peut y avoir qu'un numero de tel et pas mettre autre chose à la place
- S'occuper du afficher_numero dans EmprunteurModel

Lien du collapsible html/css : https://dev.to/jordanfinners/creating-a-collapsible-section-with-nothing-but-html-4ip9

- Done 
        -Drop liste sur retard rendu et emprunt
        - bouton rendre -> passe EST_RENDU de 0 à 1 et passe dateretour de "dateprévu" à "Now()"
        - Emprunter des exemplaires et plus que des ressources
        - Ne pas afficher le bouton emprunter si :
                - L'utilisateur à emprunter la ressource et ne l'a pas rendu
                - Il n'y a plus d'exemplaire pour la ressource de disponible
                - L'utilisateur n'est pas connecté
                - L'utilisateur a déjà emprunter 3 livres (reviens au point limiter le nombre d'emprunts)
        - Limiter le nombre d'emprunts