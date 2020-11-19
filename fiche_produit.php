<?php
require_once 'includes/init.inc.php';
require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';

$req = $bdd->prepare("SELECT DISTINCT categorie FROM boutique.produit");
$req->execute();

$categories = $req->fetchAll(PDO::FETCH_NUM);

if(isset($_GET['id_produit']) && !empty($_GET['id_produit']))
{
    $req = $bdd->prepare("SELECT * FROM boutique.produit WHERE id_produit = :id_produit");
    $req->bindValue(':id_produit', $_GET['id_produit']);
    $req->execute();

    if(!$req->rowCount())
    {
        header('location:'.URL.'boutique.php');
    }

    $produit = $req->fetch(PDO::FETCH_ASSOC);
}
else
{
    header('location:'.URL.'boutique.php');
}
?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <div class="col-lg-3">
            <h1 class="my-4">Shop Name</h1>
            <div class="list-group">
                <li class="list-group-item bg-dark text-white text-center">CATEGORIES</li>
                <?php foreach($categories as $category): ?>
                    <a href="<?= URL ?>boutique.php?categorie=<?= $category[0] ?>" class="list-group-item"><?= ucfirst($category[0]) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- /.col-lg-3 -->

        <?php if(isset($produit)): ?>
        <div class="col-lg-9">
            <div class="card mt-4">
                <img class="card-img-top img-fluid" src="<?= $produit['photo'] ?>" alt="<?= $produit['titre'] ?>">
                <div class="card-body">
                    <h3 class="card-title"><?= $produit['titre'] ?></h3>
                    <h4><?= $produit['prix'] ?>€</h4>
                    <p class="card-text"><?= $produit['description'] ?></p>
                    <p class="card-text">Catégorie : <a href='<?= URL ?>boutique.php?categorie=<?= $produit['categorie'] ?>'><?= $produit['categorie'] ?></a></p>
                    <p class="card-text">Référence : <?= $produit['reference'] ?></p>
                    <p class="card-text">Couleur : <?= $produit['couleur'] ?></p>
                    <p class="card-text">Taille : <?= $produit['taille'] ?></p>
                    <p class="card-text">Public : <?= $produit['public'] ?></p>
                    <?php if($produit['stock'] <= 10 && $produit['stock'] != 0 ): ?>
                        <p class="card-text font-italic text-danger">Attention ! Plus que <strong><?= $produit['stock'] ?> exemplaire(s)</strong> en stock !</p>
                    <?php elseif($produit['stock'] > 10): ?>
                        <p class="card-text text-success"><strong>Produit en stock</strong></p>
                    <?php endif ?>

                    <hr>

                    <?php if($produit['stock'] > 0): ?>
                        <form action="panier.php" method="post" class="form-inline">
                            <input type="hidden" name="id_produit" id="id_produit" value="<?= $produit['id_produit'] ?>">
                            <div class="form-group">
                                <select name="quantite" id="quantite" class="form-control">
                                    <?php for($i = 1; $i <= $produit['stock'] && $i <= 30; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <button class="button btn btn-success ml-2" name="ajout_panier">Ajouter au panier</button>
                        </form>
                    <?php else: ?>
                        <p class="card-text text-danger"><strong>Rupture de stock !</strong></p>
                    <?php endif ?>
                </div>
            </div>
            <?php endif ?>
            <!-- /.card -->

            <div class="card card-outline-secondary my-4">
                <div class="card-header">
                    Product Reviews
                </div>
                <div class="card-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
                    <small class="text-muted">Posted by Anonymous on 3/1/17</small>
                    <hr>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
                    <small class="text-muted">Posted by Anonymous on 3/1/17</small>
                    <hr>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
                    <small class="text-muted">Posted by Anonymous on 3/1/17</small>
                    <hr>
                    <a href="#" class="btn btn-success">Leave a Review</a>
                </div>
            </div>
            <!-- /.card -->

        </div>
        <!-- /.col-lg-9 -->

    </div>

</div>
<!-- /.container -->

<?php require_once 'includes/footer.inc.php'?>
