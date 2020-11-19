<?php
require_once 'includes/init.inc.php';

if(!is_auth() || !isset($_SESSION['num_cmd']))
{
    header('location:'.URL.'boutique.php');
}

require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';

?>

    <h1 class="display-1 text-center my-5">Félicitation !</h1>
    <h3 class="text-center">Votre commande n° <?php if(isset($_SESSION['num_cmd'])) echo $_SESSION['num_cmd'][0]; ?> a bien été prise en compte</h3>
    <h4 class="text-center">Vous recevrez un email de confirmation dans quelque instants.</h4>

    <p class="text-center">
        <a href="boutique.php" class="btn btn-success">Retour vers la boutique</a>
    </p>

<?php
unset($_SESSION['num_cmd']);
require_once 'includes/footer.inc.php'?>