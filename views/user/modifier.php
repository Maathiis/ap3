<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification d'Informations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .section {
            width: 45%;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        select,
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: blue !important;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            opacity: 1;
        }

        button:hover {
            background-color: green !important;
        }

        .right-section {
            margin-left: 20px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .section {
                width: 100%;
                margin-bottom: 20px;
            }

            .right-section {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Section de gauche -->
        <div class="section">
            <h2>Modifier les informations personnelles</h2>
            <form method="POST" action="/modifier ">
                <div>

                    <input type="text" id="idemprunteur" name="idemprunteur" value="<?= $user->idemprunteur ?>" readonly visibility : hidden>

                    <label for="genre">Genre :</label>
                    <select id="genre" name="genre">
                        <option value="M" <?= ($user->genre === 'M') ? 'selected' : '' ?>>M</option>
                        <option value="Mme" <?= ($user->genre === 'Mme') ? 'selected' : '' ?>>Mme</option>
                        <option value="Non Précisé" <?= ($user->genre === 'Non Précisé') ? 'selected' : '' ?>>Non Précisé</option>
                    </select>

                </div>
                <div>
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?= $user->nomemprunteur ?>">
                </div>
                <div>
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?= $user->prenomemprunteur ?>">
                </div>
                <div>
                    <label for="email">Adresse e-mail :</label>
                    <input type="email" id="email" name="email" value="<?= $user->emailemprunteur ?>">
                </div>
                <div>
                    <label for="tel">Numéro de téléphone :</label>
                    <input type="tel" id="tel" name="tel" value="<?= $user->telportable ?>">
                </div>
                <button type="submit" value="AppelModifier">Sauvegarder les modifications</button> <!-- Bouton Valider pour le formulaire de gauche -->
            </form>
        </div>

        <!-- Section de droite -->
        <div class="section right-section">
            <h2>Modifier le mot de passe</h2>
            <form method="POST" action="/modifierMDP">
                <input type="text" id="idemprunteur" name="idemprunteur" value="<?= $user->idemprunteur ?>" readonly visibility : hidden>
                <div>
                    <label for="oldMDP">Ancien mot de passe :</label>
                    <input type="password" id="oldMDP" name="oldMDP">
                </div>
                <div>
                    <label for="newMDP">Nouveau mot de passe :</label>
                    <input type="password" id="newMDP" name="newMDP">
                </div>
                <div>
                    <label for="confirmMDP">Confirmation du nouveau mot de passe :</label>
                    <input type="password" id="confirmMDP" name="confirmMDP">
                </div>
                <button type="submit">Sauvegarder les modifications</button> <!-- Bouton Valider pour le formulaire de droite -->
            </form>
            <br>
            <br>
            <div>
                <?php if (isset($message)) : ?>
                    <p><?= $message ?></p>
                <?php endif ?>
            </div>
        </div>
    </div>
</body>

</html>