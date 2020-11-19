<?php
require_once '../includes/init.inc.php';

if(!is_admin() || !isset($_GET['id_commande']) || empty($_GET['id_commande']))
{
    header('location:' . URL . 'admin/gestion_commandes.php');
}

if(isset($_GET['id_commande']) || !empty($_GET['id_commande']))
{
    $req = $bdd->prepare("SELECT id_commande FROM commande WHERE id_commande = :id_commande ");
    $req->bindValue(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
    $req->execute();

    if($req->rowCount() == 0)
    {
        header('location:' . URL . 'admin/gestion_commandes.php');
    }
}

$req = $bdd->prepare("SELECT commande_id as 'commande', photo, titre, quantite, produit.prix FROM boutique.produit INNER JOIN boutique.details_commande ON id_produit = produit_id WHERE commande_id = :id_commande");
$req->bindValue(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
$req->execute();

$produits = $req->fetchAll(PDO::FETCH_ASSOC);

$totalProduit = [];
$total = 0;
require_once '../includes/header.inc.php';
require '../includes/nav.inc.php';
?>

<h1 class="text-center my-5">Détails de la commande n° <?php if(isset($_GET['id_commande'])) echo $_GET['id_commande'] ?></h1>


<table class="table col-md-10 mx-auto text-center">
    <thead class="thead-light">
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
        <?php foreach ($produits as $produit):
            $totalProduit[] = $produit['quantite'] * $produit['prix'];
            ?>
            <tr>
                <?php foreach($produit as $k => $value): ?>
                    <?php if($k == 'photo'): ?>
                        <td><img src="<?= $value ?>" alt="<?= $produit['titre'] ?>" width="60px"></td>
                    <?php elseif($k =='prix'): ?>
                        <td class="align-middle"><?= $value ?>€</td>
                    <?php else: ?>
                        <td class="align-middle"><?= $value ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                <td class="align-middle"><a href="?action=modifier&id_commande=<?= $produit['commande'] ?>"><i class="fas fa-pen text-dark"></i></a></td>
                <td class="align-middle"><a href="?action=suppression&id_commande=<?= $produit['commande'] ?>" ><i class="fas fa-trash text-dark"></i></a></td>
            </tr>
        <?php endforeach ?>
            <tr>
                <th colspan="4"></th>
                <th>PRIX TOTAL</th>
                <th colspan="2"></th>
            </tr>
            <tr>
                <td colspan="4" class="border-top-0"></td>
                <?php foreach($totalProduit as $prix)
                    $total += $prix
                ?>
                <td class="border-top-0 border-bottom"><?= $total ?>€</td>
            </tr>
    </tbody>
</table>

<?php require_once '../includes/footer.inc.php' ?>
