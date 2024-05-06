<!-- Pied de page -->
<?php use utils\SessionHelpers; ?>
<footer class="bg-[#15171A] text-center py-4">
<p class="text-white">
        © 2023 Médiathèque. Tous droits réservés.
        <?php
        $emprunteur = SessionHelpers::getConnected();
        if (SessionHelpers::isLogin() && $emprunteur->developpeur == 1) {
            echo '- <a href="/api" class="text-white hover:underline">Accès développeur</a>';
        }
        ?>
    </p>
</footer>
</body>

</html>