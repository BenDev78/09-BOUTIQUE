<?php
require_once 'includes/init.inc.php';

if(is_auth())
{
    header('location: profil.php');
}

// lorsque nous cliquons sur le bouton de déconnexion, il transmet en même temps dans l'URL 'action=deconnexion'
// La condition if permet de vérifier si l'indice 'action' est bien définit dans l'URL et qu'il a pour valeur 'déconnexion'
// Dans le cas ou il rentre dans la condition on supprime l'index 'user' du tableau $_SESSION
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion' )
{
    unset($_SESSION['user']);
}

if($_POST)
{
    $data = $bdd->prepare("SELECT * FROM boutique.membre WHERE pseudo = :pseudo OR email = :email");
    $data->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR);
    $data->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);
    $data->execute();

    if(!$data->rowCount())
    {
        $error = true;
        $errorConnexion = "<p class='bg-danger col-md-3 p-2 mx-auto text-white text-center rounded font-italic'>Identifiant ou mot de passe incorrect</p>";
    }
    else
    {
        // echo "pseudo ou email existant";
        $user = $data->fetch(PDO::FETCH_ASSOC);

        //Contrôle mot de passe en clair
        //$_POST['password'] == $user['mdp']


        // password_verify() permet de comparer une clé de hachage à une chaine de caractères
        // arguments : password_verify('la chaine de caractère à comparer', 'la clé de hachage')
        if(password_verify($_POST['password'], $user['mdp']))
        {
            // echo "Ok";
            // SI nous entrons dans cette condition, cela veut dire que l'internaute à bien rentré ses identifiant


            // On passe en revue toutes les données de l'internaute qui s'est connecté
            // $user : tableau contenant toutes les données utilisateur en BDD
            foreach($user as $key => $value)
            {
                if($key != 'mdp') // on exclu le mot de passe du fichier session
                {
                    // insertion des données du user dans la session
                    // on crée dans la session une indice 'user' contenant un tableau avec toutes les données de l'utilisateur
                    // C'est ce qui permettra d'identifier l'utilisateur connecté sur le site et cela lui permettra de naviguer sur le site tout en restant connecté
                    $_SESSION['user'][$key] = $value;
                }
            }

            header('location: profil.php');

        }
        else
        {
            $error = true;
            $errorConnexion = "<p class='bg-danger col-md-3 p-2 mx-auto text-white text-center rounded font-italic'>Identifiant ou mot de passe incorrect</p>";
        }
    }
}

require_once 'includes/header.inc.php';
require 'includes/nav.inc.php';
?>


<h1 class="display-4 text-center my-4">Identifiez-vous</h1>
<?php if(isset($errorConnexion)) echo $errorConnexion ?>
<form class="col-md-4 mx-auto" method="post">
    <div class="form-group">
        <label for="pseudo_email">Pseudo / Email</label>
        <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" value="<?php if(isset($_POST['pseudo_email'])) echo $_POST['pseudo_email'] ?>">
    </div>
    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="form-group text-center">
        <button type="submit" class="btn btn-dark">Connexion</button>
    </div>
</form>

<?php require_once 'includes/footer.inc.php'?>

