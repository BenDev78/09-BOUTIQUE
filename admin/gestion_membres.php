<?php
require_once '../includes/init.inc.php';

if(!is_admin())
{
    header('location:' . URL . 'connexion.php');
}

$req = $bdd->query("SELECT id_membre AS ID,nom,pseudo,email,civilite,ville,code_postal AS 'CODE POSTAL',adresse,statut as ROLE FROM boutique.membre");
$membres = $req->fetchAll(PDO::FETCH_ASSOC);

if(isset($_GET['action']) && $_GET['action'] == 'modifier')
{
    $req = $bdd->prepare("SELECT * FROM boutique.membre WHERE id_membre = :id_membre");
    $req->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
    $req->execute();

    $m = $req->fetch(PDO::FETCH_ASSOC);
}
if($_POST)
{
    foreach($_POST as $k => $value)
    {
        $_POST[$k] = strip_tags($value);
    }

    $req = $bdd->prepare("SELECT * FROM boutique.membre WHERE pseudo = :pseudo AND id_membre != :id_membre");
    $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $req->bindValue(':id_membre', $_GET['id_membre']);
    $req->execute();

    if($req->rowCount())
    {
        $error = true;

        $_SESSION['msg'] = "<p class='bg-danger font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Le pseudo <strong>$_POST[pseudo]</strong> est déjà utilisé</p>";
    }

    if(isset($_GET['action']) && $_GET['action'] == 'modifier')
    {
        if(isset($_GET['id_membre']) && !empty($_GET['id_membre'] && !isset($error))) {
            $req = $bdd->prepare("UPDATE boutique.membre SET pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, ville = :ville, code_postal = :code_postal, adresse = :adresse, statut = :statut WHERE id_membre = :id_membre");

            $req->bindValue(':pseudo', strtolower($_POST['pseudo']), PDO::PARAM_STR);
            $req->bindValue(':nom', $m['nom'], PDO::PARAM_STR);
            $req->bindValue(':prenom', $m['prenom'], PDO::PARAM_STR);
            $req->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $req->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
            $req->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
            $req->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
            $req->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
            $req->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);
            $req->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);

            $req->execute();

            $_SESSION['msg'] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Le membre ID $_GET[id_membre] a été modifié avec succès</p>";

            header('location:' . URL . 'admin/gestion_membres.php');
        }
    }
    else
    {
        echo "erreur";
        header('location:' . URL . 'admin/gestion_membres.php');
    }

}

if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_membre']))
{
    $req = $bdd->prepare("DELETE FROM boutique.membre WHERE id_membre = :id_membre");
    $req->bindValue(":id_membre", $_GET['id_membre'], PDO::PARAM_INT);
    $req->execute();

    $_SESSION['msg'] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Le membre ID ".$_GET['id_membre']. " a bien été modifié</p>";

    header('location:'. URL .'admin/gestion_membres.php');
}

$nbMembre = $bdd->query("SELECT * FROM boutique.membre");
$count = $nbMembre->rowCount();
if($count == 1)
{
    $txt = "membre inscrit";
}
else
{
    $txt = "membres inscrits";
}

require_once '../includes/header.inc.php';
require '../includes/nav.inc.php';
?>

<?php if(isset($_GET['action']) && $_GET['action'] == 'modifier'): ?>
    <h1 class="text-center my-5">Modification du membre</h1>
<?php else: ?>
    <h1 class="text-center my-5">Affichage des membres</h1>
<?php endif ?>

<div class="text-center">
    <?php
    if(isset($_SESSION['msg']))
    {
        echo $_SESSION['msg'] ;
    }
    if (!isset($_GET['action']))
    {
        unset($_SESSION['msg']);
    }
    ?>
</div>

<?php if(!isset($_GET['action'])): ?>
<h5 class="col-md-10 mx-auto p-0"><span class="badge badge-success "><?= $count ?></span>&nbsp;<?= $txt ?></h5>
<?php endif ?>
<?php if(!isset($_GET['action'])): ?>
<table class="table table-bordered col-md-10 mx-auto my-5 text-center">
    <thead class="thead-dark">
    <tr>
        <?php for($i = 0; $i < $req->columnCount(); $i++):
            $colonnes = $req->getColumnMeta($i)
        ?>
            <?php if($colonnes['name'] != 'mdp'): ?>
            <th><?= strtoupper($colonnes['name']) ?></th>
            <?php endif ?>
        <?php endfor ?>
        <th>Modifier</th>
        <th>Supprimer</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($membres as $membre): ?>
    <tr>
        <?php foreach($membre as $k => $value): ?>
            <?php if($k != 'mdp' && $k != 'ROLE'): ?>
                <td class="align-middle"><?= $value ?></td>
            <?php endif ?>

            <!-- Change numerical value to string -->
            <?= memberStatusValue($k, $value) ?>

        <?php endforeach; ?>
        <td class="align-middle"><a href="?action=modifier&id_membre=<?= $membre['ID'] ?>"><i class="fas fa-pen text-warning"></i></a></td>
        <td class="align-middle"><a href="?action=suppression&id_membre=<?= $membre['ID'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce membre ? ')"><i class="fas fa-times text-danger"></i></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>

<div style="box-shadow: 0px 0px 10px -2px #333333;" class="col-md-6 mx-auto py-3 rounded">

    <form class="mx-auto" action="" method="post">

        <div class="form-groupe mb-4">
            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php if (isset($m)) echo $m['pseudo'] ?>">
        </div>

        <div class="form-row">
            <div class="form-groupe col-md-6 mb-4">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" value="<?php if (isset($m)) echo $m['nom'] ?>" disabled>
            </div>
            <div class="form-groupe col-md-6 mb-4">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" class="form-control" value="<?php if (isset($m)) echo $m['prenom'] ?>" disabled>
            </div>
        </div>

        <div class="form-row">
            <div class="form-groupe col-md-6 mb-4">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="<?php if (isset($m)) echo $m['email'] ?>">
            </div>
            <div class="form-groupe col-md-6 mb-4">
                <label for="civilite">Civilité</label>
                <select name="civilite" id="civilite" class="form-control">
                    <option value="homme">Homme</option>
                    <option value="femme" <?php if(isset($m['civilite']) && $m['civilite'] == 'femme') echo 'selected' ?>>Femme</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-groupe col-md-6 mb-4">
                <label for="ville">Ville</label>
                <input type="text" name="ville" id="ville" class="form-control" value="<?php if (isset($m)) echo $m['ville'] ?>">
            </div>
            <div class="form-groupe col-md-6 mb-4">
                <label for="code_postal">Code postal</label>
                <input type="number" name="code_postal" id="code_postal" value="<?php if (isset($m)) echo (int)$m['code_postal'] ?>" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-groupe col-md-6 mb-4">
                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" class="form-control" value="<?php if (isset($m)) echo $m['adresse'] ?>">
            </div>
            <div class="form-groupe col-md-6 mb-4">
                <label for="statut">Code postal</label>
                <select name="statut" id="statut" class="form-control">
                    <option value="0" <?php if(isset($m['statut']) && $m['statut'] == 0 ) echo 'selected' ?>>Membre</option>
                    <option value="1" <?php if(isset($m['statut']) && $m['statut'] == 1 ) echo 'selected' ?>>Admin</option>
                </select>
            </div>
        </div>

        <div class="form-group text-center">
            <button class="btn btn-dark w-25">Modifier</button>
        </div>

    </form>

</div>

<?php endif ?>
<?php require_once '../includes/footer.inc.php'?>
