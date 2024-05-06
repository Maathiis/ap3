<style>
    .emprunt-rendu {
        border: 2px solid green;
    }

    .emprunt-expire {
        border: 2px solid red;
    }

    .emprunt-attente {
        border: 2px solid white;
    }
    
    details {
  user-select: none; 
}

summary {
  display: flex;
  cursor: pointer;
}

summary::-webkit-details-marker {
  display: none;
}
</style>

<div class="container mx-auto py-8 min-h-[calc(100vh-136px)]">
    <div class="flex flex-wrap">
        <!-- Colonne de gauche -->
        <div class="w-full md:w-1/3 px-4">
            <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg px-6 py-4">
                <div class="flex items-center justify-center mb-4">
                    <img src="<?= \utils\Gravatar::get_gravatar($user->emailemprunteur) ?>" alt="Photo de profil" class="rounded-full h-32 w-32">
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">👋 <?= $user->prenomemprunteur ?></h1>
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Informations personnelles</h2>
                    <p class="text-gray-600 mb-2"><span class="font-semibold">Email:</span> <?= $user->emailemprunteur ?></p>
                    <p class="text-gray-600 mb-2"><span class="font-semibold">Nom:</span> <?= $user->nomemprunteur ?>
                    </p>
                    <p class="text-gray-600 mb-2"><span class="font-semibold">Prénom:</span> <?= $user->prenomemprunteur ?></p>
                    <p class="text-gray-600 mb-2"><span class="font-semibold">Téléphone:</span>
                        <?php
                        // Vérifiez si l'utilisateur a choisi de masquer son numéro de téléphone
                        if ($user->afficher_numero === "oui") {
                            echo $user->telportable;
                        } else {
                            echo "Numéro masqué";
                        }
                        ?></p>
                    <?php
                    // Vérifiez si $nbrRessourceEmprunte existe et a un élément à l'indice 0
                    if (!empty($nbrRessourceEmprunte) && isset($nbrRessourceEmprunte[0])) {
                        $nbRessourcesEmpruntees = $nbrRessourceEmprunte[0]->nb_ressources_empruntees;
                    } else {
                        // Si $nbrRessourceEmprunte n'existe pas ou n'a pas d'élément à l'indice 0, définissez $nbRessourcesEmpruntees sur 0
                        $nbRessourcesEmpruntees = 0;
                    }
                    ?>
                    <p>Nombre de ressources empruntées : <?= $nbRessourcesEmpruntees; ?></p>
                </div>

                <div class="p-5 text-center flex space-x-5 justify-center">
                    <a class="bg-blue-600 text-white hover:bg-blue-900 font-bold py-3 px-6 rounded-full" href="/modifier">
                        Modifier
                    </a>
                    <a class="bg-green-600 text-white hover:bg-green-900 font-bold py-3 px-6 rounded-full" href="/me/telecharger">
                        Télécharger
                    </a>
                    <a class="bg-red-600 text-white hover-bg-red-900 font-bold py-3 px-6 rounded-full" href="/logout">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>

        <!-- Colonne de droite -->
        <div class="w-full md:w-2/3 px-4 mt-6 md:mt-0">



            <!-- Liste des RETARDS -->
            <?php
            $empruntsEnRetard = array_filter($emprunts, function($emprunt) {
                $dateRetour = strtotime($emprunt->dateretour);
                return $dateRetour < time() && !$emprunt->EST_RENDU;
            });
            ?>

            <?php if (!empty($empruntsEnRetard)) : ?>
            <details>
            <summary>
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes retards</h1>
            </div>
            </summary>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-5">
                <?php foreach ($emprunts as $emprunt) : ?>
                    <?php
                    $dateRetour = strtotime($emprunt->dateretour);
                    if ($dateRetour < time() && !$emprunt->EST_RENDU) :
                    ?>
                        <div class="bg-white shadow-lg rounded-lg px-6 py-4 emprunt-expire">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= $emprunt->titre ?></h2>
                            <p class="text-gray-600 mb-2">Type: <span class="font-semibold"><?= $emprunt->libellecategorie ?></span></p>
                            <p class="text-gray-600 mb-2">
                                Date d'emprunt:
                                <span class="font-semibold"><?= date_format(date_create($emprunt->datedebutemprunt), "d/m/Y") ?></span>
                            </p>
                            <p class="text-gray-600 mb-2">
                                Date de retour prévue:
                                <span class="font-semibold"><?= date_format(date_create($emprunt->dateretour), "d/m/Y") ?></span>
                            </p>
                            <?php
                            $estRendu = $emprunt->EST_RENDU;
                            $dateRetour = strtotime($emprunt->dateretour);
                            $pénalitéParSemaine = 1;
                            $tempsDeRetard = time() - $dateRetour;
                            $message = "";

                            if ($tempsDeRetard > 0) {
                                $nombreDeSemainesDeRetard = floor($tempsDeRetard / (7 * 24 * 60 * 60)); // Calcul du nombre de semaines de retard
                                $pénalité = $pénalitéParSemaine * $nombreDeSemainesDeRetard + 1; // Calcul de la pénalité
                            }

                            if ($dateRetour < time() &&  $estRendu == 0) {
                                $message = "Pénalité pour retard de : " . $pénalité . "€";
                            }
                            ?>
                            <p><?= $message ?></p>
                            <div class="flex items-center">
                                <a class="m-2 py-1 px-2 rounded-full text-blue-700 bg-blue-100 border border-blue-300" href="/catalogue/detail/<?= $emprunt->idressource ?>">Fiche</a>
                                <?php

                                $etatEmprunt = 'Retard';
                                $class = "m-2 py-1 px-2 rounded-full text-red-700 bg-red-100 border border-red-300";


                                ?>
                                <p class="<?= $class ?>"><?= $etatEmprunt ?></p>
                                <a class="m-2 py-1 px-2 rounded-full text-green-700 bg-green-100 border border-green-300" href="/rendre/<?= $emprunt->idressource ?>/<?= $emprunt->idexemplaire ?>">
                                    Rendre
                                </a>
                            </div>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
            </details>
            <?php endif ?>
            <!-- Fin RETARD -->



            <!-- Liste des emprunts EN COURS -->
            <details open> <!-- Open, affiche les emprunts par défaut -->
            <summary>
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes emprunts</h1>
            </div>
            </summary>

            <?php if (!$emprunts) { ?>
                <!-- Message si aucun emprunt -->
                <div class="bg-white shadow-lg rounded-lg px-6 py-4 mt-5">
                    <p class="text-gray-600 mb-2">Vous n'avez aucun emprunt en cours.</p>
                </div>
            <?php } else { ?>
                <!-- Tableau des emprunts -->
                <div id="tabEmprunt" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-5">

                    <?php foreach ($emprunts as $emprunt) : ?>
                        <?php
                        $dateRetour = strtotime($emprunt->dateretour);
                        if ($emprunt->EST_RENDU == 0 && $dateRetour > time()) :
                        ?>
                            <div class="bg-white shadow-lg rounded-lg px-6 py-4 emprunt-attente">
                                <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= $emprunt->titre ?></h2>
                                <p class="text-gray-600 mb-2">Type: <span class="font-semibold"><?= $emprunt->libellecategorie ?></span></p>
                                <p class="text-gray-600 mb-2">
                                    Date d'emprunt:
                                    <span class="font-semibold"><?= date_format(date_create($emprunt->datedebutemprunt), "d/m/Y") ?></span>
                                </p>
                                <p class="text-gray-600 mb-2">
                                    Date de retour prévue:
                                    <span class="font-semibold"><?= date_format(date_create($emprunt->dateretour), "d/m/Y") ?></span>
                                </p>
                                <div class="flex items-center">
                                    <a class="m-2 py-1 px-2 rounded-full text-blue-700 bg-blue-100 border border-blue-300" href="/catalogue/detail/<?= $emprunt->idressource ?>">Fiche</a>
                                    <?php
                                        $etatEmprunt = 'En cours';
                                        $class = "m-2 py-1 px-2 rounded-full text-blue-700 bg-blue-100 border border-blue-300";
                                    ?>
                                    <p class="<?= $class ?>"><?= $etatEmprunt ?></p>
                                   
                                    <a class="m-2 py-1 px-2 rounded-full text-green-700 bg-green-100 border border-green-300" href="/rendre/<?= $emprunt->idressource ?>/<?= $emprunt->idexemplaire ?>">
                                        Rendre
                                    </a>
                                </div>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
            <?php } ?>
            </details>
            <!-- Fin EN COURS -->
            


            <!-- Liste des HISTORIQUES -->
            <?php
            $empruntsRendus = array_filter($emprunts, function($emprunt) {
                return $emprunt->EST_RENDU;
            });
            ?>

            <?php if (!empty($empruntsRendus)) : ?>
            <details>
            <summary>
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes historiques d'emprunts </h1>
            </div>
            </summary>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-5">

                <?php foreach ($emprunts as $emprunt) : ?>
                    <?php
                    if ($emprunt->EST_RENDU == 1) :
                    ?>
                    <div class="bg-white shadow-lg rounded-lg px-6 py-4 emprunt-rendu">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= $emprunt->titre ?></h2>
                        <p class="text-gray-600 mb-2">Type: <span class="font-semibold"><?= $emprunt->libellecategorie ?></span></p>
                        <p class="text-gray-600 mb-2">
                            Retourné le :
                            <span class="font-semibold"><?= date_format(date_create($emprunt->dateretour), "d/m/Y") ?></span>
                        </p>
                        <div class="flex items-center">
                            <a class="m-2 py-1 px-2 rounded-full text-blue-700 bg-blue-100 border border-blue-300" href="/catalogue/detail/<?= $emprunt->idressource ?>">Fiche</a>
                            <?php
                                $etatEmprunt = 'Rendu';
                                $class = "m-2 py-1 px-2 rounded-full text-green-700 bg-green-100 border border-green-300";
                            ?>
                            <p class="<?= $class ?>"><?= $etatEmprunt ?></p>
                        </div>
                    </div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
            </details>
            <?php endif ?>
            <!-- Fin HISTORIQUE -->

        </div>
    </div>
</div>

<script>

    document.querySelector("#exemplaire").addEventListener("submit", async (e) => {
        e.preventDefault()
        const result = await Swal.fire({
            title: 'Confirmer la remise ?',
            text: "Souhaitez-vous rendre cette ressource ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
        })
        if (result.isConfirmed) {
            e.target.submit()
        }
    });

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
