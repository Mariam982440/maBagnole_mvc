
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaBagnole | Ajouter un article</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-slate-800 text-white shadow-md p-4">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="../index" class="text-xl font-bold">MaBagnole Blog 🚗</a>
            <div class="flex items-center gap-4">
                <span class="text-sm italic">Bonjour, <?= htmlspecialchars($user_nom) ?></span>
                <a href="../index" class="text-xs bg-slate-700 px-3 py-1 rounded hover:bg-slate-600 transition">Retour</a>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto my-10 p-8 bg-white rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">Proposer un nouvel article</h2>
        
        <?= $message ?>

        <form action="../index/ajouter" method="POST" class="space-y-5">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre de l'article *</label>
                <input type="text" name="titre" required
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Choisir un thème *</label>
                <select name="theme" class="w-full border-gray-300 border p-2.5 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    <?php foreach($themeTab as $t): ?>
                        <option value="<?= $t['id_theme'] ?>">
                            <?= htmlspecialchars($t['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenu de l'article *</label>
                <textarea name="contenu" rows="6" required
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lien de l'image</label>
                <input type="text" name="image_url" placeholder="https://exemple.com/image.jpg"
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div class="pt-4">
                <button type="submit" 
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 shadow-md transition-all active:scale-95">
                    Soumettre pour approbation
                </button>
            </div>
        </form>
    </div>

</body>
</html>