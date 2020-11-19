<?php require_once 'functions.inc.php'?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Ma Boutique en ligne</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample04">
        <ul class="navbar-nav mr-auto">

            <!-- Go to definition to add more links -->
            <?php navMenu(); ?>

            <?php if(is_admin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Back office</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown04">
                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_boutique.php?action=affichage">Gestion boutique</a>
                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_commandes.php">Gestion commandes</a>
                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_membres.php">Gestion membres</a>
                    </div>
                </li>
            <?php endif ?>
        </ul>
        <form class="form-inline my-2 my-md-0">
            <input class="form-control" type="text" placeholder="Search">
        </form>
    </div>
</nav>

<main class="container-fluid p-0" style="min-height: 88vh">

