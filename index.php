<?php
// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Gestion onglets
$tab = $_GET['tab'] ?? 'livres';

// Récupération données pour modification

// Livres
$edit_livre = null;
if (isset($_GET['edit_livre'])) {
    $id = (int)$_GET['edit_livre'];
    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id_livre = ?");
    $stmt->execute([$id]);
    $edit_livre = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Écrivains
$edit_ecrivain = null;
if (isset($_GET['edit_ecrivain'])) {
    $id = (int)$_GET['edit_ecrivain'];
    $stmt = $pdo->prepare("SELECT * FROM ecrivains WHERE id_ecrivain = ?");
    $stmt->execute([$id]);
    $edit_ecrivain = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Utilisateurs
$edit_utilisateur = null;
if (isset($_GET['edit_utilisateur'])) {
    $id = (int)$_GET['edit_utilisateur'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$id]);
    $edit_utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Emprunts
$edit_emprunt = null;
if (isset($_GET['edit_emprunt'])) {
    $id = (int)$_GET['edit_emprunt'];
    $stmt = $pdo->prepare("SELECT * FROM emprunts WHERE id_emprunt = ?");
    $stmt->execute([$id]);
    $edit_emprunt = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Gestions des formulaires

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Livres
    if (isset($_POST['save_livre'])) {
        $titre = $_POST['titre'];
        $annee = (int)$_POST['annee'];
        $id_genre = (int)$_POST['id_genre'];
        $id_ecrivain = (int)$_POST['id_ecrivain'];
        if (!empty($_POST['id_livre'])) {
            // Modifier
            $id_livre = (int)$_POST['id_livre'];
            $stmt = $pdo->prepare("UPDATE livres SET titre = ?, annee = ?, id_genre = ?, id_ecrivain = ? WHERE id_livre = ?");
            $stmt->execute([$titre, $annee, $id_genre, $id_ecrivain, $id_livre]);
        } else {
            // Ajouter
            $stmt = $pdo->prepare("INSERT INTO livres (titre, annee, id_genre, id_ecrivain) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titre, $annee, $id_genre, $id_ecrivain]);
        }
        header('Location: ?tab=livres');
        exit;
    }

    // Écrivains
    if (isset($_POST['save_ecrivain'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $nationalite = $_POST['nationalite'];
        if (!empty($_POST['id_ecrivain'])) {
            $id_ecrivain = (int)$_POST['id_ecrivain'];
            $stmt = $pdo->prepare("UPDATE ecrivains SET nom = ?, prenom = ?, nationalite = ? WHERE id_ecrivain = ?");
            $stmt->execute([$nom, $prenom, $nationalite, $id_ecrivain]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO ecrivains (nom, prenom, nationalite) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $prenom, $nationalite]);
        }
        header('Location: ?tab=ecrivains');
        exit;
    }

    // Utilisateurs
    if (isset($_POST['save_utilisateur'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        if (!empty($_POST['id_utilisateur'])) {
            $id_utilisateur = (int)$_POST['id_utilisateur'];
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?");
            $stmt->execute([$nom, $prenom, $email, $id_utilisateur]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email]);
        }
        header('Location: ?tab=utilisateurs');
        exit;
    }

    // Emprunts
    if (isset($_POST['save_emprunt'])) {
        $id_livre = (int)$_POST['id_livre'];
        $id_utilisateur = (int)$_POST['id_utilisateur'];
        $date_emprunt = $_POST['date_emprunt'];
        $date_retour = $_POST['date_retour'] ?? null;
        if (!empty($_POST['id_emprunt'])) {
            $id_emprunt = (int)$_POST['id_emprunt'];
            $stmt = $pdo->prepare("UPDATE emprunts SET id_livre = ?, id_utilisateur = ?, date_emprunt = ?, date_retour = ? WHERE id_emprunt = ?");
            $stmt->execute([$id_livre, $id_utilisateur, $date_emprunt, $date_retour, $id_emprunt]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO emprunts (id_livre, id_utilisateur, date_emprunt, date_retour) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_livre, $id_utilisateur, $date_emprunt, $date_retour]);
        }
        header('Location: ?tab=emprunts');
        exit;
    }
}

// Suppressions (via GET)

if (isset($_GET['delete_livre'])) {
    $id = (int)$_GET['delete_livre'];
    $pdo->prepare("DELETE FROM livres WHERE id_livre = ?")->execute([$id]);
    header('Location: ?tab=livres');
    exit;
}

if (isset($_GET['delete_ecrivain'])) {
    $id = (int)$_GET['delete_ecrivain'];
    $pdo->prepare("DELETE FROM ecrivains WHERE id_ecrivain = ?")->execute([$id]);
    header('Location: ?tab=ecrivains');
    exit;
}

if (isset($_GET['delete_utilisateur'])) {
    $id = (int)$_GET['delete_utilisateur'];
    $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?")->execute([$id]);
    header('Location: ?tab=utilisateurs');
    exit;
}

if (isset($_GET['delete_emprunt'])) {
    $id = (int)$_GET['delete_emprunt'];
    $pdo->prepare("DELETE FROM emprunts WHERE id_emprunt = ?")->execute([$id]);
    header('Location: ?tab=emprunts');
    exit;
}

// Récupération données affichage
$genres = $pdo->query("SELECT * FROM genres")->fetchAll(PDO::FETCH_ASSOC);
$ecrivains = $pdo->query("SELECT * FROM ecrivains")->fetchAll(PDO::FETCH_ASSOC);
$livres = $pdo->query("SELECT livres.*, genres.nom_genre, ecrivains.nom AS nom_ecrivain, ecrivains.prenom AS prenom_ecrivain FROM livres 
                       LEFT JOIN genres ON livres.id_genre = genres.id_genre 
                       LEFT JOIN ecrivains ON livres.id_ecrivain = ecrivains.id_ecrivain")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
$emprunts = $pdo->query("SELECT emprunts.*, livres.titre AS titre_livre, utilisateurs.nom AS nom_user, utilisateurs.prenom AS prenom_user FROM emprunts 
                        LEFT JOIN livres ON emprunts.id_livre = livres.id_livre
                        LEFT JOIN utilisateurs ON emprunts.id_utilisateur = utilisateurs.id_utilisateur")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Bibliothèque</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
    <h1>Gestion de la Bibliothèque</h1>
    <nav>
        <a href="?tab=livres" class="<?= $tab === 'livres' ? 'active' : '' ?>">Livres</a>
        <a href="?tab=ecrivains" class="<?= $tab === 'ecrivains' ? 'active' : '' ?>">Écrivains</a>
        <a href="?tab=utilisateurs" class="<?= $tab === 'utilisateurs' ? 'active' : '' ?>">Utilisateurs</a>
        <a href="?tab=emprunts" class="<?= $tab === 'emprunts' ? 'active' : '' ?>">Emprunts</a>
    </nav>
</header>

<main>

<?php if ($tab === 'livres'): ?>
    <h2>Livres</h2>
    <form method="post" class="form-inline">
        <input type="hidden" name="id_livre" value="<?= $edit_livre['id_livre'] ?? '' ?>" />
        <input type="text" name="titre" placeholder="Titre" required value="<?= h($edit_livre['titre'] ?? '') ?>" />
        <input type="number" name="annee" placeholder="Année" min="1000" max="<?= date('Y') + 10 ?>" required value="<?= h($edit_livre['annee'] ?? '') ?>" />
        <select name="id_genre" required>
            <option value="">Genre</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= $genre['id_genre'] ?>" <?= (isset($edit_livre) && $edit_livre['id_genre'] == $genre['id_genre']) ? 'selected' : '' ?>>
                    <?= h($genre['nom_genre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="id_ecrivain" required>
            <option value="">Écrivain</option>
            <?php foreach ($ecrivains as $e): ?>
                <option value="<?= $e['id_ecrivain'] ?>" <?= (isset($edit_livre) && $edit_livre['id_ecrivain'] == $e['id_ecrivain']) ? 'selected' : '' ?>>
                    <?= h($e['prenom'] . ' ' . $e['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="save_livre" class="btn btn-primary">
            <?= isset($edit_livre) ? 'Modifier' : 'Ajouter' ?>
        </button>
        <?php if(isset($edit_livre)): ?>
            <a href="?tab=livres" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Année</th>
                <th>Genre</th>
                <th>Écrivain</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td><?= h($livre['titre']) ?></td>
                    <td><?= h($livre['annee']) ?></td>
                    <td><?= h($livre['nom_genre']) ?></td>
                    <td><?= h($livre['prenom_ecrivain'] . ' ' . $livre['nom_ecrivain']) ?></td>
                    <td>
                        <a href="?tab=livres&edit_livre=<?= $livre['id_livre'] ?>" class="btn btn-warning">Modifier</a>
                        <a href="?tab=livres&delete_livre=<?= $livre['id_livre'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce livre ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($tab === 'ecrivains'): ?>
    <h2>Écrivains</h2>
    <form method="post" class="form-inline">
        <input type="hidden" name="id_ecrivain" value="<?= $edit_ecrivain['id_ecrivain'] ?? '' ?>" />
        <input type="text" name="nom" placeholder="Nom" required value="<?= h($edit_ecrivain['nom'] ?? '') ?>" />
        <input type="text" name="prenom" placeholder="Prénom" required value="<?= h($edit_ecrivain['prenom'] ?? '') ?>" />
        <input type="text" name="nationalite" placeholder="Nationalité" value="<?= h($edit_ecrivain['nationalite'] ?? '') ?>" />
        <button type="submit" name="save_ecrivain" class="btn btn-primary">
            <?= isset($edit_ecrivain) ? 'Modifier' : 'Ajouter' ?>
        </button>
        <?php if(isset($edit_ecrivain)): ?>
            <a href="?tab=ecrivains" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Nationalité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ecrivains as $e): ?>
                <tr>
                    <td><?= h($e['nom']) ?></td>
                    <td><?= h($e['prenom']) ?></td>
                    <td><?= h($e['nationalite']) ?></td>
                    <td>
                        <a href="?tab=ecrivains&edit_ecrivain=<?= $e['id_ecrivain'] ?>" class="btn btn-warning">Modifier</a>
                        <a href="?tab=ecrivains&delete_ecrivain=<?= $e['id_ecrivain'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet écrivain ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($tab === 'utilisateurs'): ?>
    <h2>Utilisateurs</h2>
    <form method="post" class="form-inline">
        <input type="hidden" name="id_utilisateur" value="<?= $edit_utilisateur['id_utilisateur'] ?? '' ?>" />
        <input type="text" name="nom" placeholder="Nom" required value="<?= h($edit_utilisateur['nom'] ?? '') ?>" />
        <input type="text" name="prenom" placeholder="Prénom" required value="<?= h($edit_utilisateur['prenom'] ?? '') ?>" />
        <input type="email" name="email" placeholder="Email" required value="<?= h($edit_utilisateur['email'] ?? '') ?>" />
        <button type="submit" name="save_utilisateur" class="btn btn-primary">
            <?= isset($edit_utilisateur) ? 'Modifier' : 'Ajouter' ?>
        </button>
        <?php if(isset($edit_utilisateur)): ?>
            <a href="?tab=utilisateurs" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td><?= h($u['nom']) ?></td>
                    <td><?= h($u['prenom']) ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td>
                        <a href="?tab=utilisateurs&edit_utilisateur=<?= $u['id_utilisateur'] ?>" class="btn btn-warning">Modifier</a>
                        <a href="?tab=utilisateurs&delete_utilisateur=<?= $u['id_utilisateur'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($tab === 'emprunts'): ?>
    <h2>Emprunts</h2>
    <form method="post" class="form-inline">
        <input type="hidden" name="id_emprunt" value="<?= $edit_emprunt['id_emprunt'] ?? '' ?>" />
        <select name="id_livre" required>
            <option value="">Livre</option>
            <?php foreach ($livres as $livre): ?>
                <option value="<?= $livre['id_livre'] ?>" <?= (isset($edit_emprunt) && $edit_emprunt['id_livre'] == $livre['id_livre']) ? 'selected' : '' ?>>
                    <?= h($livre['titre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="id_utilisateur" required>
            <option value="">Utilisateur</option>
            <?php foreach ($utilisateurs as $u): ?>
                <option value="<?= $u['id_utilisateur'] ?>" <?= (isset($edit_emprunt) && $edit_emprunt['id_utilisateur'] == $u['id_utilisateur']) ? 'selected' : '' ?>>
                    <?= h($u['prenom'] . ' ' . $u['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>Date emprunt</label>
        <input type="date" name="date_emprunt" value="<?= isset($edit_emprunt) ? h($edit_emprunt['date_emprunt']) : date('Y-m-d') ?>" required />
        <label>Date retour</label>
        <input type="date" name="date_retour" value="<?= $edit_emprunt['date_retour'] ?? '' ?>" />
        <button type="submit" name="save_emprunt" class="btn btn-primary">
            <?= isset($edit_emprunt) ? 'Modifier' : 'Ajouter' ?>
        </button>
        <?php if(isset($edit_emprunt)): ?>
            <a href="?tab=emprunts" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Livre</th>
                <th>Utilisateur</th>
                <th>Date emprunt</th>
                <th>Date retour</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt): ?>
                <tr>
                    <td><?= h($emprunt['titre_livre']) ?></td>
                    <td><?= h($emprunt['prenom_user'] . ' ' . $emprunt['nom_user']) ?></td>
                    <td><?= h($emprunt['date_emprunt']) ?></td>
                    <td><?= h($emprunt['date_retour'] ?? '') ?></td>
                    <td>
                        <a href="?tab=emprunts&edit_emprunt=<?= $emprunt['id_emprunt'] ?>" class="btn btn-warning">Modifier</a>
                        <a href="?tab=emprunts&delete_emprunt=<?= $emprunt['id_emprunt'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet emprunt ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

</main>
</body>
</html>
