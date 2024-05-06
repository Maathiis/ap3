<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emprunt - MediaTout</title>
</head>
<body>
    <h1>Récapitulatif</h1>
    <p>
        Voici le récapitulatif de votre emprunt.
    </p>
    <ul>
        <li><strong>Ressources empruntée : </strong> <?= $titreEmprunt ?></li>
        <li><strong>Date d'emprunt : </strong> <?= $debutEmprunt ?></li>
        <li><strong>Date de retour : </strong> <?= $finEmprunt ?></li>
    </ul>

    <p>
        Nous contacter : 01 23 45 67 89
        A bientôt sur notre site !
    </p>
</body>
</html>