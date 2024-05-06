<div class="container mx-auto py-8 min-h-[calc(100vh-136px)]">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag/dist/css/multi-select-tag.css">
    <h2 class="text-3xl font-bold text-gray-800 mb-4"><?= $titre ?></h2>

    <form action="/catalogue/tri" method="get">
        <div class="mb-10 mt-10 filtrage">
            <select name="categories[]" id="categories[]" multiple >
                <?php foreach ($categories as $categorie) { ?>
                    <option value="<?= $categorie->idcategorie ?>"><?= $categorie->libellecategorie ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn bg-green-600 text-white hover:bg-green-900 py-1 px-3 m-2">Filtrer</button>
        </div>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 container mx-auto">
        <?php foreach ($catalogue as $ressource) { ?>

            <a href="/catalogue/detail/<?= $ressource->idressource ?>" class="bg-white rounded-lg shadow-lg">
                <img loading="lazy" src="/public/assets/<?= $ressource->image ?>"
                     alt="<?= htmlspecialchars($ressource->titre) ?>"
                     class="w-full h-64 object-cover object-center rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate"><?= $ressource->titre ?></h3>
                    <div class ="flex">
                        <div class="w-fit flex justify-center items-center font-medium py-1 px-2 bg-white rounded-full text-blue-700 bg-blue-100 border border-blue-300 mr-2">
                            <div class="text-xs font-normal leading-none max-w-full flex-initial">
                                <?= $ressource->libellecategorie ?>
                            </div>
                        </div>
                    </div>   
                </div>
            </a>

        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag/dist/js/multi-select-tag.js"></script>
<script>
    new MultiSelectTag('categories[]')
</script>