<?php
require_once 'includes/init.inc.php';
if(!is_auth())
{
    header('location: connexion.php');
}

extract($_SESSION['user']);
require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';
?>


<h1 class="text-center display-4">Bonjour <?= ucfirst($_SESSION['user']['pseudo']) ?></h1>
<h4 class="text-center m-4">Nous somme ravi de vous revoir</h4>

<div class="card col-md-3 mx-auto">
    <h4 class="card-title text-center my-3 ">Vos infos Personnelles</h4>
        <div class="card-body">
            <ul>
                <?php foreach ($_SESSION['user'] as $k => $value): ?>
                    <?php if($k != 'id_membre' && $k != 'statut'): ?>
                        <li><strong><?= $k?></strong> : <?= $value ?></li>
                    <?php endif ?>
                <?php endforeach; ?>
            </ul>
            <a href="#" class="text-info">Modifier vos informations</a>
        </div>
</div>

<?php require_once 'includes/footer.inc.php'?>
