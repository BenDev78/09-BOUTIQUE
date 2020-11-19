<?php
require_once '../includes/init.inc.php';

if(!is_admin())
{
    header('location:' . URL . 'connexion.php');
}

$req = $bdd->query("SELECT id_commande as ID, id_membre as 'N° CLIENT', email, nom, prenom, adresse, montant, DATE_FORMAT(date_enregistrement, '%d/%m/%Y à %H:%i:%s') AS DATE,etat FROM boutique.commande INNER JOIN boutique.membre ON membre_id = id_membre");
$commandes = $req->fetchAll(PDO::FETCH_ASSOC);
//dump($commandes);

require_once '../includes/header.inc.php';
require '../includes/nav.inc.php';

// Exo : afficher la liste des commandes sous forme de tableau htlm contenant les colonnes suivantes:
/*
 * - id_commande
 * - nom
 * - prenom
 * - email
 * - montant total
 * - date enregistrement
 * - etat
 * - edit, detail, supp
 * Jointure sql entre la table commande et la table membre
 */

?>
<h1 class="text-center my-5">Liste des commandes</h1>

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
        <th>MODIFIER</th>
        <th>DETAIL</th>
        <th>SUPPRIMER</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($commandes as $commande): ?>
        <tr>
            <?php foreach($commande as $k => $value): ?>
                <td class="align-middle"><?= $value ?></td>
            <?php endforeach; ?>
            <td class="align-middle"><a href="?action=modifier&id_commande=<?= $commande['ID'] ?>"><i class="fas fa-pen text-dark"></i></a></td>
            <td class="align-middle"><a href="<?= URL ?>admin/fiche_commande.php?id_commande=<?= $commande['ID'] ?>"><i class="fas fa-list text-dark"></i></a></td>
            <td class="align-middle"><a href="?action=suppression&id_commande=<?= $commande['ID'] ?>" ><i class="fas fa-trash text-dark"></i></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

<?php require_once '../includes/footer.inc.php'?>
