<?php use utils\SessionHelpers; ?>

<div class="container mx-auto py-8 min-h-[calc(100vh-136px)]">
    <div class="flex flex-wrap">
        <!-- Colonne de gauche -->
        <div class="w-full md:w-1/2 px-4">
            <img src="/public/assets/<?= $ressource->image ?>"
                 alt="Image du livre"
                 class="mb-4 rounded-lg object-cover m-auto h-[70vh]">
        </div>

        <!-- Colonne de droite -->
        <!-- Détails de l'emprunt  -->
        <div class="w-full md:w-1/2 px-4 mt-6 md:mt-0">
            <div class="bg-white shadow-lg rounded-lg px-6 py-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= $ressource->titre ?></h1>
                <p class="text-gray-600 mb-2">Année de publication: <span class="font-semibold"><?= $ressource->anneesortie ?></span></p>
                <p class="text-gray-600 mb-2">Langue : <span class="font-semibold"><?= $ressource->langue ?></span></p>
                <p class="text-gray-600 mb-2">ISBN : <span class="font-semibold"><?= $ressource->isbn ?></span></p>
                <p class="text-gray-600 mb-2">Description: <span class="font-semibold">
                        <?= $ressource->description ?>
                </p>

                <!-- Bouton pour emprunter un exemplaire -->
                <?php if ($exemplaire) { ?> 
                    <?php if (SessionHelpers::isConnected() && $showEmprunter == true)   { ?>   
                            <form id="exemplaire" method="post" class="text-center pt-5 pb-3" action="/catalogue/emprunter">
                                <input type="hidden" name="idRessource" value="<?= $ressource->idressource ?>">
                                <input type="hidden" name="idExemplaire" value="<?= $exemplaire->idexemplaire ?>">
                                <button type="submit"
                                        class="bg-indigo-600 text-white hover:bg-indigo-900 font-bold py-3 px-6 rounded-full">
                                    Emprunter
                                </button>
                            </form>
                    <?php } else if (!SessionHelpers::isConnected()) { ?> 
                        <p class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded-md my-4 mx-auto max-w-md flex items-center justify-center">Créez un compte pour voir nos exemplaires.</p>
                    <?php } else if ($nbExemplaireDispo == 0) { ?> 
                        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-md my-4 mx-auto max-w-md flex items-center justify-center">Tous nos exemplaires ont été empruntés.</p>
                    <?php } else if ($getNbEmprunt = 3) { ?>
                        <p class="bg-purple-100 border border-purple-400 text-purple-700 px-4 py-2 rounded-md my-4 text-center">Vous avez atteint le maximum de ressource empruntable simultanément.</p>
                    <?php }  ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- Poster un commentaires -->
<div class="bg-white shadow-lg rounded-lg px-6 py-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Commentaires</h1>
                <!-- Limiter l'utilisateur à ne poster qu'un commentaire par emprunt / Permettre modification du msg ? signaler modification-->
                <?php if (SessionHelpers::isLogin()) { ?>
                <form method="POST" id="poster_commentaire" action="/sendcom/<?= $ressource->idressource ?>">
                    <input type="text" class="text-gray-600 mb-2" value="<?= $user->prenomemprunteur ?> <?= $user->nomemprunteur ?>" disabled></input><br>
                    <input type="text" class="text-gray-600 mb-2" value="<?= date("d/m/Y") ?>" disabled></input><br>
                    <input name="idEmprunteur" type="hidden" class="text-gray-600 mb-2" value="<?= $user->idemprunteur  ?>"></input><br>
                    <input name="idRessource" type="hidden" class="text-gray-600 mb-2" value="<?= $ressource->idressource ?>"></input><br>
                    <textarea name="commentaire" placeholder="Votre commentaire..."></textarea><br/>
                    <input type="submit" value="Poster mon commentaire" name="submit_commentaire" />
                </form>
                <?php } ?>
</div>
<!-- Section commentaires -->
<div class="bg-white shadow-lg rounded-lg px-6 py-4">
    <?php usort($commentaires, function($a, $b) {
        return strtotime($b->getDate_commentaire()) - strtotime($a->getDate_commentaire());
    });
    foreach ($commentaires as $commentaire) : ?>
        <?php if ($commentaire->getIdRessource() == $ressource->idressource) { ?>
        <!-- + profil gravatar-->
        <p class="text-gray-600 mb-2"><?= $commentaire->getIdEmprunteur() ?></p> <!-- Je veux que le nom soit affiché pas l'id -->
        <p class="text-gray-600 mb-2"><?= $commentaire->getDate_commentaire() ?></p> <!-- + étoiles sur la même ligne -->
        <p class="text-gray-600 mb-2"><?= $commentaire->getCommentaire() ?></p>
        <?php } ?>
    <?php endforeach ?>
</div>

<script>

    document.querySelector("#exemplaire").addEventListener("submit", async (e) => {
        e.preventDefault()
        const result = await Swal.fire({
            title: 'Confirmer l\'emprunt ?',
            text: "Souhaitez-vous emprunter cette ressource ?",
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

<!-- J'ai réussi à afficher tous les commentaires mais faut faire du CSS + faire en sorte que ça soit les comms que des livres en question-->