<?php
function dump($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

// Function user logged in
function is_auth()
{
    if(!isset($_SESSION['user']))
    {
        return false;
    }
    else
    {
        return true;
    }
}

// Function user ADMIN logged in
function is_admin()
{
    if(is_auth() && $_SESSION['user']['statut'] == 1)
    {
        return true;
    }
    return false;
}

function addCartToSession()
{
    if(!isset($_SESSION['panier']))
    {
        $_SESSION['panier'] = [];
        $_SESSION['panier']['id_produit'] = [];
        $_SESSION['panier']['photo'] = [];
        $_SESSION['panier']['reference'] = [];
        $_SESSION['panier']['titre'] = [];
        $_SESSION['panier']['quantite'] = [];
        $_SESSION['panier']['prix'] = [];
    }
}

function addProductsInCart($id, $photo, $reference, $titre, $quantite, $prix)
{
    addCartToSession();

    $positionProduit = array_search($id, $_SESSION['panier']['id_produit']);

    if($positionProduit !== false)
    {
        $_SESSION['panier']['quantite'][$positionProduit] += $quantite;
    }
    else
    {
        $_SESSION['panier']['id_produit'][] = $id ;
        $_SESSION['panier']['photo'][] = $photo ;
        $_SESSION['panier']['reference'][] = $reference ;
        $_SESSION['panier']['titre'][] = $titre ;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
    }

}

function totalProduits()
{
    $total = 0;

    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return $total;
}

function productAmount()
{
    if(isset($_SESSION['panier'])){
        return "(" . $sumProduct = array_sum($_SESSION['panier']['quantite']) . ")";
    }
    else
    {
        return "(0)";
    }
}

function deleteProductFromCart($id_produit)
{
    $indexProduct = array_search($id_produit, $_SESSION['panier']['id_produit']);

    if($indexProduct !== false)
    {
        if(isset($_GET['action']) && $_GET['action'] == 'suppression'){
            $_SESSION['msg'][] = "<p class='bg-success font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Le produit <strong>". $_SESSION['panier']['titre'][$indexProduct] ."</strong> - référence <strong>". $_SESSION['panier']['reference'][$indexProduct]."</strong> été supprimé</p>";
        }

        // array_splice() permet de supprimer des éléments d'un tableau ARRAY
        // on supprime chaque ligne dans les tableaux ARRAY du produit en rupture de stock
        // array_splice() ré-organise les tbaleaux ARRAY, c'est à  dire que tout les élément aux indices inférieur remonttent aux indices supérieurs, le produit stocké à l'indice 3 du teablau ARRAY remonte à l'indice 2 du tableau ARRAY
        array_splice($_SESSION['panier']['id_produit'], $indexProduct, 1);
        array_splice($_SESSION['panier']['photo'], $indexProduct, 1);
        array_splice($_SESSION['panier']['reference'], $indexProduct, 1);
        array_splice($_SESSION['panier']['titre'], $indexProduct, 1);
        array_splice($_SESSION['panier']['quantite'], $indexProduct, 1);
        array_splice($_SESSION['panier']['prix'], $indexProduct, 1);

    }
    else
    {
        $_SESSION['msg'][] = "<p class='bg-danger font-italic col-md-3 text-center mx-auto p-2 rounded text-white'>Une erreur est survenue, le produit n'a pas été supprimé</p>";

        header('location:'. URL .'panier.php');
    }
}

// Change numerical member status value to string
function memberStatusValue($k, $value)
{
    if($k == 'ROLE')
    {
        if($value == 0)
        {
            return "<td class='align-middle'>MEMBRE</td>";
        }
        else
        {
            return "<td class='align-middle bg-info text-white'>ADMIN</td>";
        }
    }
}

function navItem($name, $link)
{
    $class = '';
    if( $_SERVER['SCRIPT_NAME'] == $link)
    {
        $class = ' active';
    }
    echo "<li class='nav-item$class'>";
    echo "<a class='nav-link ' href='$link'>$name</a>";
    echo "</li>";
}

function navMenu()
{
    $url = '/09-BOUTIQUE/';
    if(is_auth())
    {
        navItem('Boutique', $url.'boutique.php');
        navItem('Votre Panier', $url.'panier.php');
        navItem('Déconnexion', $url.'connexion.php?action=deconnexion');
    }
    else
    {
        navItem('Créez votre compte', $url.'inscription.php');
        navItem('Identifiez-vous', $url.'connexion.php');
        navItem('Boutique', $url.'boutique.php');
        navItem('Votre panier', $url.'panier.php');
    }

}