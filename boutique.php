<?php
require_once 'includes/init.inc.php';
require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';

// On sélectionne les catégories en éliminant les doublons en BDD
$req = $bdd->prepare("SELECT DISTINCT categorie FROM boutique.produit ");
$req->execute();

$categories = $req->fetchAll(PDO::FETCH_NUM);

if(isset($_GET['categorie']) && !empty($_GET['categorie']))
{
    $req = $bdd->prepare("SELECT * FROM boutique.produit WHERE categorie = :categorie");
    $req->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $req->execute();
    $count = $req->rowCount();
    if(!$req->rowCount())
    {
        header('location:'.URL.'boutique.php');
    }
    $produits = $req->fetchAll(PDO::FETCH_ASSOC);
}
else
{
    $req = $bdd->query("SELECT * FROM boutique.produit");
    $produits = $req->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!-- Page Content -->
<div class="container mb-5">

    <div class="row">

        <div class="col-lg-3">

            <h1 class="my-4">Shop Name</h1>

            <div class="list-group">
                <li class="list-group-item bg-dark text-white text-center">CATEGORIES</li>
                <?php foreach($categories as $category): ?>
                    <a href="?categorie=<?= $category[0] ?>" class="list-group-item text-center"><?= ucfirst($category[0]) ?></a>
                <?php endforeach; ?>
                <?php if(isset($_GET['categorie'])): ?>
                    <p class="text-center font-italic">Nombre d'article(s) trouvé(s) : <small>(<?= $count ?>)</small></p>
                <?php endif ?>
            </div>

        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-9">

            <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img class="d-block img-fluid" src="<?= URL ?>assets/slider1.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="<?= URL ?>assets/slider2.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="<?= URL ?>assets/slider3.jpg" alt="Third slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

            <div class="row">

                <?php foreach ($produits as $produit): ?>
<!--                --><?php //dump($produit); ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <a href="<?= URL ?>fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>"><img class="card-img-top" src="<?= $produit['photo'] ?>" alt="<?= $produit['titre'] ?>"></a>
                        <div class="card-body">
                            <h4 class="card-title">
                                <a href="<?= URL ?>fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>"><?= ucfirst($produit['titre']) ?></a>
                            </h4>
                            <h5><?= $produit['prix'] ?> €</h5>
                            <p class="card-text">
                                <?php if(strlen($produit['description']) > 80)
                                        echo substr($produit['description'], 0, 50). '...' ;
                                    else
                                        echo $produit['description']
                                ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= URL ?>fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>" class="btn btn-info">Voir le détail &raquo;</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.col-lg-9 -->

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->

<?php require_once 'includes/footer.inc.php'?>
