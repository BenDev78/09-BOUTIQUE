<?php
require_once '../includes/init.inc.php';

// session parpage
// session filtre
// session key
if(!isset($_SESSION['filtres']))
{
    $_SESSION['filtres']['parpage'] = 10;
    $_SESSION['filtres']['filtre'] = ' id_produit ';
    $_SESSION['filtres']['key'] = 'filtre1';
}
//TODO: refaire la session avec une clé ['filtres'][] ...

if(!is_admin())
{
    header('location:' . URL . 'connexion.php');
}

$data = $bdd->prepare("SELECT * FROM boutique.produit ORDER BY id_produit");
$data->execute();
$count = $data->rowCount();
$produits = $data->fetchAll(PDO::FETCH_ASSOC);

// Enregistrement Produit
if($_POST && !isset($_POST['filtrer']))
{
    //----------TRAITEMENT DE LA PHOTO------------//
    $photoBdd = '';

    if(isset($_GET['action']) && $_GET['action'] == 'modifier')
    {
        $photoBdd = $_POST['photo_actuelle'];
    }

    if (!empty($_FILES['photo']['name']))
    {

        // Renomme l'image en concaténant la référence saisie dans le formulaire et le nom de la photo récupérée dans $_FILES
        $nomPhoto = $_POST['reference'] . '-' . $_FILES['photo']['name'];
        // echo $nomPhoto;

        // on définit L'URL de la photo qui sera enregistré en BDD
        $photoBdd = URL . "assets/$nomPhoto";
        // echo $photoBdd;

        // On définit le chemin physique de la photo vers le dossier photo sur le serveur, ce qui nous permet de copier l'image dans le bon dossier
        $photoDossier = ROOT . "assets/$nomPhoto";
        //echo $photoDossier;


        // copy() : fonction prédéfinie permettant de copier un fichier
        // arguments:
        // 1. le nom temporaire de l'image accessible dans $_FILES
        // 2. le chemin physique de la photo jusqu'au dossier assets sur le serveur
        copy($_FILES['photo']['tmp_name'], $photoDossier);
    }

    //----------INSERTION | MODIFICATION BDD------------//
    if(!isset($error))
    {
        extract($_POST);

        if(isset($_GET['action']) && $_GET['action'] == 'ajout')
        {
            $req = $bdd->prepare("INSERT INTO boutique.produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) 
                                          VALUES(:reference, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock)");

            $_SESSION['msg'] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Produit bien ajouté</p>";
        }
        else
        {
            $req = $bdd->prepare("UPDATE boutique.produit SET 
                            reference = :reference,
                            categorie = :categorie, 
                            titre = :titre, 
                            description = :description,
                            couleur = :couleur,
                            photo = :photo,
                            taille = :taille,
                            public = :public,
                            prix = :prix, 
                            stock = :stock WHERE id_produit = :id_produit");

            $req->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);

            $_SESSION['msg'] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Produit ID$_GET[id_produit] a bien été modifié</p>";
        }

        $req->bindValue(':reference', $reference, PDO::PARAM_STR);
        $req->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        $req->bindValue(':titre', $titre, PDO::PARAM_STR);
        $req->bindValue(':description', $description, PDO::PARAM_STR);
        $req->bindValue(':couleur', $couleur, PDO::PARAM_STR);
        $req->bindValue(':taille', $taille, PDO::PARAM_STR);
        $req->bindValue(':public', $public, PDO::PARAM_STR);
        $req->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
        $req->bindValue(':prix', $prix, PDO::PARAM_INT);
        $req->bindValue(':stock', $stock, PDO::PARAM_INT);

        $req->execute();

        header('location:'. URL .'admin/gestion_boutique.php?action=affichage');
    }

}

if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    $req = $bdd->prepare("DELETE from boutique.produit WHERE id_produit = :id_produit");
    $req->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $req->execute();

    $_SESSION['msg'] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Produit ID$_GET[id_produit] bien été supprimé</p>";

    header('location:'. URL .'admin/gestion_boutique?action=affichage');
}

//----------PAGINATION / FILTRAGE------------//

if(isset($_GET['page']) && !empty($_GET['page']))
{
    $currentPage = (int)strip_tags($_GET['page']);
}
else
{
    $currentPage = 1;
}

// RECUPERATION NOMBRE PRODUITS
$req = $bdd->prepare("SELECT COUNT(*) AS nb_produits FROM produit ");
$req->execute();
$result = $req->fetch();

$nbArticles = $result['nb_produits'];

//Tableau filtres
$filtresSelect = [
    'filtre1' => 'ID Produit',
    'filtre2' => 'Prix croissant',
    'filtre3' => 'Prix Décroissant',
    'filtre4' => 'Catégorie',
    'filtre5' => 'Couleur'
];
$filtre = $_SESSION['filtres']['filtre'];

// Clé pour 'selected' des filtres
$fil = $_SESSION['filtres']['key'];

if(isset($_POST['filtre']))
{
    switch ($_POST['filtre'])
    {
        case 'filtre1':
            $_SESSION['filtres']['filtre'] = ' id_produit ';
            $_SESSION['filtres']['key'] = 'filtre1';
            break;
        case 'filtre2':
            $_SESSION['filtres']['filtre'] = ' prix ';
            $_SESSION['filtres']['key'] = 'filtre2';
            break;
        case 'filtre3':
            $_SESSION['filtres']['filtre'] = ' prix DESC ';
            $_SESSION['filtres']['key'] = 'filtre3';
            break;
        case 'filtre4':
            $_SESSION['filtres']['filtre'] = ' categorie ';
            $_SESSION['filtres']['key'] = 'filtre4';
            break;
        case 'filtre5':
            $_SESSION['filtres']['filtre'] = ' couleur ';
            $_SESSION['filtres']['key'] = 'filtre5';
            break;
        default:
            $_SESSION['filtres']['filtre'] = ' id_produit ';
            $_SESSION['filtres']['key'] = 'filtre1';
    }
}

// Nombre de page
if(!is_numeric($_SESSION['filtres']['parpage']))
{
    $_SESSION['filtres']['parpage'] = 10;
}
$parPage = (int)$_SESSION['filtres']['parpage'];

if(isset($_POST['parpage']))
{
    $_SESSION['filtres']['parpage'] = $_POST['parpage'];

    header('location:'.URL.'admin/gestion_boutique.php?action=affichage');
}

//Nombres max de pages
$pages = ceil($nbArticles / $parPage);

//Calcul premier article
$premier = ($currentPage * $parPage) - $parPage;

// LIMITATION PRODUITS PAR PAGE
$req = $bdd->prepare('SELECT * FROM produit ORDER BY '.$filtre.' LIMIT :premier, :parpage');

$req->bindValue(':premier', $premier, PDO::PARAM_INT);
$req->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$req->execute();
$produits = $req->fetchAll(PDO::FETCH_ASSOC);

dump($_SESSION);
require_once '../includes/header.inc.php';
require '../includes/nav.inc.php';

?>

<!--###############################  HTML ########################################-->


<ul class=" col-md-2 mx-auto list-group text-center mt-3">
    <li class="list-group-item bg-dark text-white">BACK OFFICE</li>
    <li class="list-group-item"><a href="?action=affichage" class="col-md-8 btn btn-primary">Affichage produit</a></li>
    <li class="list-group-item"><a href="?action=ajout" class="col-md-8 btn btn-primary">Ajout produit</a></li>
</ul>

<?php if(isset($_GET['action']) && $_GET['action'] == 'affichage' ): ?>

    <h2 class="text-center my-3">Affichage des produits (<?= $count ?>)</h2>

<form action="" method="post" class="form-inline m-2">

    <select name="parpage" id="parpage" class="form-control mr-2">
        <?php for($i = 1; $i < 101; $i++): ?>
            <?php if($i % 10 == 0): ?>
                <option value="<?= $i ?>" <?= $parPage == $i ? 'selected' : ''; ?> > <?= $i ?></option>
            <?php endif ?>
        <?php endfor; ?>
    </select>

    <select name="filtre" id="filtre" class="form-control mr-2">
        <?php foreach($filtresSelect as $k => $value): ?>
            <option value="<?= $k ?>" <?= $k == $fil ? 'selected' : '' ?>><?= $value ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-dark" name="filtrer">Filtrer</button>

</form>

<div class="text-center">
    <?php
        if(isset($_SESSION['msg']))
        {
            echo $_SESSION['msg'] ;
        }
        unset($_SESSION['msg']);
    ?>
</div>

    <table class="table table-bordered mb-5 text-center">
        <thead class="thead-dark">
        <tr>
            <?php for($i = 0; $i < $data->columnCount(); $i++): ?>
                <?php $colonnes = $data->getColumnMeta($i) ?>
                <th><?= strtoupper($colonnes['name']) ?></th>
            <?php endfor ?>
            <th>Modifier</th>
            <th>Supprimer</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($produits as $arr): ?>
            <tr>
                <?php foreach ($arr as $k => $value): ?>
                    <?php if($k == 'photo'): ?>
                        <td><img src="<?= $value ?>" alt="" style="width: 60px"/></td>
                    <?php elseif($k == 'description'): ?>
                        <td class="align-middle"><?= substr($value, 0, 40). '...'; ?></td>
                    <?php else: ?>
                        <td class="align-middle"><?= $value ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                <td class="align-middle"><a href="?action=modifier&id_produit=<?= $arr['id_produit'] ?>"><i class="fas fa-pen text-warning"></i></a></td>
                <td class="align-middle"><a href="?action=suppression&id_produit=<?= $arr['id_produit'] ?>" onclick="confirm('Voulez-vous vraiment supprimer ce produit ? ')"><i class="fas fa-times text-danger"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation example" class="d-flex justify-content-center" style="margin-bottom: 60px;">
        <ul class="pagination">
            <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>" >
                <a class="page-link" href="<?= URL ?>admin/gestion_boutique.php?action=affichage&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <?php for($page = 1; $page <= $pages; $page++): ?>
            <li class="page-item <?= ($currentPage == $page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= URL ?>admin/gestion_boutique.php?action=affichage&page=<?= $page ?>"><?= $page ?></a>
            </li>
            <?php endfor ?>
            <li class="page-item <?= ($currentPage == $pages) ? 'disabled' : '' ?>" >
                <a class="page-link" href="<?= URL ?>admin/gestion_boutique.php?action=affichage&page=<?= $currentPage + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif ?>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier' )): ?>

<?php
    if(isset($_GET['id_produit']) && !empty($_GET['id_produit']))
    {
        $req = $bdd->prepare("SELECT * FROM boutique.produit WHERE id_produit = :id_produit");
        $req->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
        $req->execute();

        if($req->rowCount())
        {
            $produit = $req->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            header('location:'.URL.'admin/gestion_boutique?action=affichage');
        }
    }
    elseif ($_GET['action'] == 'modifier' && (!isset($_GET['id_produit']) || empty($_GET['id_produit'])))
    {
        header('location:'.URL.'admin/gestion_boutique?action=affichage');
    }
?>

    <?php if ($_GET['action'] == 'ajout'):?>
        <h1 class="text-center mb-3"> Ajouter un produit </h1>
    <?php elseif ($_GET['action'] == 'modifier') :?>
        <h1 class="text-center mb-3"> Modifier un produit </h1>
    <?php endif ?>


    <!-- enctype="multipart/form-data" : si le formulaire contient un upload de fichier, il ne faut pas oublier l'attribut 'enctype' et la valeur 'multipart/form-data' qui permettent de stocker les informations du ficher uploadé directement dans la super globale $_FILES (type, nom, extension, nom temporaire) -->
    <form class="col-md-6 mx-auto" action="" method="post" enctype="multipart/form-data">
        <div class="form-groupe">
            <label for="reference">Référence</label>
            <input type="text" name="reference" id="reference" class="form-control" value="<?php if (isset($produit)) echo $produit['reference'] ?>">
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                <div class="form-groupe">
                    <label for="categorie">Catégorie</label>
                    <select name="categorie" id="categorie" class="form-control">
                        <option value="chemises">Chemises</option>
                        <option value="pantalons" <?php if(isset($produit['categorie']) && $produit['categorie'] == 'pantalons') echo 'selected' ?>>Pantalons</option>
                        <option value="pulls" <?php if(isset($produit['categorie']) && $produit['categorie'] == 'pulls') echo 'selected' ?>>Pulls</option>
                        <option value="tee-shirts" <?php if(isset($produit['categorie']) && $produit['categorie'] == 'tee-shirts') echo 'selected' ?>>T-shirts</option>
                        <option value="manteaux" <?php if(isset($produit['categorie']) && $produit['categorie'] == 'manteaux') echo 'selected' ?>>Manteaux</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-groupe">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" class="form-control" value="<?php if (isset($produit)) echo $produit['titre'] ?>">
                </div>
            </div>
        </div>
        <hr/>
        <div class="form-groupe">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" class="form-control"><?php if (isset($produit)) echo $produit['description'] ?></textarea>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                <div class="form-groupe">
                    <label for="couleur">Couleur</label>
                    <input type="text" name="couleur" id="couleur" class="form-control" value="<?php if (isset($produit)) echo $produit['couleur'] ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-groupe">
                    <label for="taille">Taille</label>
                    <select name="taille" id="taille" class="form-control">
                        <option value="s">S</option>
                        <option value="m" <?php if(isset($produit['taille']) && $produit['taille'] == 'm') echo 'selected' ?>>M</option>
                        <option value="l" <?php if(isset($produit['taille']) && $produit['taille'] == 'l') echo 'selected' ?>>L</option>
                    </select>
                </div>
            </div>
        </div>
        <hr/>
        <div class="form-group">
            <label for="public">Public</label>
            <select name="public" id="public" class="form-control">
                <option value="homme">Homme</option>
                <option value="femme" <?php if(isset($produit['public']) && $produit['public'] == 'femme') echo 'selected' ?>>Femme</option>
                <option value="mixte" <?php if(isset($produit['public']) && $produit['public'] == 'mixte') echo 'selected' ?>>Mixte</option>
            </select>
        </div>
        <hr/>
        <div class="row">

            <?php if(!empty($produit['photo'])): ?>
                <input type="hidden" name="photo_actuelle" id="photo_actuelle" value="<?= $produit['photo'] ?>">
            <?php endif ?>

            <div class="form-group col-md-6">
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo" class="form-control">
            </div>

            <?php if(!empty($produit['photo'])): ?>
                <div class="text-center col-md-6">
                    <p>Vous pouvez uploader une nouvelle image si vous souhaitez la modifier</p>
                    <img src='<?= $produit['photo'] ?>' alt="" style="width: 200px">
                </div>
            <?php endif ?>
        </div>

        <hr/>
        <div class="row">
            <div class="col-md-6">
                <label for="prix">Prix</label>
                <input type="text" name="prix" id="prix" class="form-control" value="<?php if (isset($produit)) echo $produit['prix'] ?>">
            </div>
            <div class="col-md-6">
                <label for="stock">Stock</label>
                <input type="text" name="stock" id="stock" class="form-control" value="<?php if (isset($produit)) echo $produit['stock'] ?>">
            </div>
        </div>
        <hr>
        <div class="form-group">
            <?php if ($_GET['action'] == 'ajout'): ?>
                <button class="btn btn-dark w-100" name="ajout">Ajouter produit</button>
            <?php elseif ($_GET['action'] == 'modifier') :?>
                <button class="btn btn-dark w-100" name="modifier">Modifier produit</button>
            <?php endif ?>
        </div>
    </form>
<?php endif ?>


<?php require_once '../includes/footer.inc.php'?>
