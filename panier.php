<?php
require_once 'includes/init.inc.php';

if(isset($_POST['ajout_panier']))
{
    $req = $bdd->prepare("SELECT * FROM boutique.produit WHERE id_produit = :id_produit");
    $req->bindValue(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
    $req->execute();

    $produit = $req->fetch(PDO::FETCH_ASSOC);

    addProductsInCart(
        $produit['id_produit'],
        $produit['photo'],
        $produit['reference'],
        $produit['titre'],
        $_POST['quantite'],
        $produit['prix']
    );

}

// Contrôle stock produit
if(isset($_POST['payer']))
{
    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $req = $bdd->query("SELECT stock FROM boutique.produit WHERE id_produit =" . $_SESSION['panier']['id_produit'][$i] );
        $stock = $req->fetch(PDO::FETCH_ASSOC);
         if($stock['stock'] < $_SESSION['panier']['quantite'][$i])
         {
            $_SESSION['msg'][] = "<p class='bg-warning font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Stock restant du produit : <strong>$stock[stock]</strong> / Produits demandés <strong>"  . $_SESSION['panier']['quantite'][$i] ."</strong></p>";

            if($stock['stock'] > 0)
            {
                $_SESSION['msg'][] = "<p class='bg-danger font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>La quantité du produit <strong>" .$_SESSION['panier']['titre'][$i]. "</strong>  référence <strong>". $_SESSION['panier']['reference'][$i] ."</strong> a été modifiée car la quantité demandée est supérieur au stock restant</p>";
                $_SESSION['panier']['quantite'][$i] = $stock['stock'];
            }
            else
            {
                $_SESSION['msg'][] = "<p class='bg-danger font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>La quantité du produit <strong>" .$_SESSION['panier']['titre'][$i]. "</strong>  référence <strong>". $_SESSION['panier']['reference'][$i] ."</strong> a été supprimé car le produit est en rupture stock</p>";
                deleteProductFromCart($_SESSION['panier']['id_produit'][$i]);
                $i--;
            }
            $e = true;
         }
    }

    // SI $e est pas définit, cela veut dire que les stocks sont supérieur à la quantité demandée par l'internaute
    if(!isset($e))
    {
        // Enregistrement table commande
        $req = $bdd->exec("INSERT INTO boutique.commande (membre_id, montant, date_enregistrement) VALUES (".$_SESSION['user']['id_membre']." , ". totalProduits() ." , NOW())");

        // Permet de récupérer le dernier id_commande crée dans la base de donnée afin de l'enregistrer dans la table details_commande, pour chaque produit à la bonne commande
        $id_commande = $bdd->lastInsertId();

        // La boucle FOR tourne autant de fois qu'il y a d'id_produit dans la session, donc autant qu'il y a de produits dans le panier
        for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
            // Pour chaque tour de boucle FOR, on exécute une requête d'insertion dans la table details_commande pour chaque produit ajouté
            // On récupère le dernier id_commande généré en BDD afin de relier chaque produit à la bonne commande dans la table details_commande
            $req = $bdd->exec("INSERT INTO boutique.details_commande (commande_id, produit_id, quantite, prix) VALUES ($id_commande, ".$_SESSION['panier']['id_produit'][$i].",".$_SESSION['panier']['quantite'][$i].",".$_SESSION['panier']['prix'][$i].")");

            // Dépréciation des stocks
            // Modifie la table 'produit' afin que le stock soit égal au stock de la BDD MOINS la quantité du produit commandé A CONDITION que l'id_produit de la BDD soit égal à l'id_produit du produit stocké dans le panier
            $req = $bdd->exec("UPDATE boutique.produit SET stock = stock - " . $_SESSION['panier']['quantite'][$i] . " WHERE id_produit = " . $_SESSION['panier']['id_produit'][$i]);
        }
        unset($_SESSION['panier']);

        $_SESSION['num_cmd'] = $id_commande;
        header('location:'.URL.'validation_cmd.php');
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_produit']))
{
    deleteProductFromCart($_GET['id_produit']);
}

require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';
?>

<h1 class="text-center display-4 my-5">Votre panier</h1>

<div class="text-center">
    <?php
    if(isset($_SESSION['msg']))
    {
        foreach ($_SESSION['msg'] as $msg)
        {
            echo $msg ;
        }
        unset($_SESSION['msg']);
    }
    ?>
</div>
    <table class="col-md-8 mx-auto table table-bordered text-center">
            <thead>
                <tr>
                    <th>PHOTO</th>
                    <th>REFERENCE</th>
                    <th>TITRE</th>
                    <th>QUANTITE</th>
                    <th>PRIX UNITAIRE</th>
                    <th>PRIX TOTAL</th>
                    <th>SUPPRIMER</th>
                </tr>
            </thead>
        <tbody>
        <?php if(empty($_SESSION['panier']['id_produit'])): ?>
            <tr><td colspan="7" class="text-danger">Votre panier est vide</td></tr>
        <?php else: ?>
            <?php foreach ($_SESSION['panier']['id_produit'] as $k => $value): ?>
                <tr>
                    <td><a href="<?= URL ?>fiche_produit.php?id_produit=<?= $_SESSION['panier']['id_produit'][$k] ?>"><img src="<?= $_SESSION['panier']['photo'][$k] ?>" alt="$_SESSION['panier']['titre'][$k]" width="100px"></a></td>
                    <td class="align-middle"><?= $_SESSION['panier']['reference'][$k] ?></td>
                    <td class="align-middle"><?= $_SESSION['panier']['titre'][$k] ?></td>
                    <td class="align-middle"><?= $_SESSION['panier']['quantite'][$k] ?></td>
                    <td class="align-middle"><?= $_SESSION['panier']['prix'][$k] ?> €</td>
                    <td class="align-middle"><?= $_SESSION['panier']['prix'][$k] * $_SESSION['panier']['quantite'][$k] ?> €</td>
                    <td class="align-middle">
                        <a href="?action=suppression&id_produit=<?= $_SESSION['panier']['id_produit'][$k] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet article ?')">
                            <i class="fas fa-times text-danger"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
                <tr>
                    <td colspan="5"></td>
                    <td>Total TTC : <?= totalProduits() ?></td>
                </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php if(is_auth() && !empty($_SESSION['panier']['id_produit'])): ?>

    <form action="" method="post" class="col-md-8 mx-auto">
        <div class="form-group">
            <input type="submit" name="payer" value="Valider le panier" class="btn btn-dark">
        </div>
    </form>
    <?php elseif(!is_auth()): ?>
        <p class="text-center">Pour valider votre panier, merci de vous <a href="<?= URL ?>connexion.php">connecter</a></p>
<?php endif ?>



<?php require_once 'includes/footer.inc.php'?>
