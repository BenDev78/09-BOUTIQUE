<?php
require_once 'includes/init.inc.php';
if(is_auth())
{
    header('location: profil.php');
}
$error = false;

if($_POST)
{
    foreach($_POST as $k => $value)
    {
        $_POST[$k] = strip_tags($value);
    }

    $pseudo = strtolower($_POST['pseudo']);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mdp = $_POST['mdp'];
    $confirm_mdp = $_POST['confirm_mdp'];
    $email = $_POST['email'];
    $civilite = $_POST['civilite'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $code_postal = $_POST['code_postal'];

    // Validation pseudo
    if(!empty($pseudo) && strlen($pseudo) < 20)
    {

        $verifPseudo = $bdd->prepare("SELECT * FROM boutique.membre WHERE pseudo = :pseudo");
        $verifPseudo->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $verifPseudo->execute();

        if($verifPseudo->rowCount() == 1)
        {
            $error = true;
            $errorPseudo = "<span class='text-danger font-italic'>Ce pseudo est déjà utilisé</span>";
        }
    }else{
        $error = true;
        $errorPseudo = "<span class='text-danger font-italic'>Ce champ ne peut être vide et doit être inférieur à 20 caractères</span>";
    }

    // Validation email
    if(filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email))
    {
        $verifEmail = $bdd->prepare("SELECT email FROM boutique.membre WHERE email = :email");
        $verifEmail->bindValue(':email', $email, PDO::PARAM_STR);
        $verifEmail->execute();
        if($verifEmail->rowCount() == 1)
        {
            $error = true;
            $errorMail = "<span class='text-danger font-italic'>Cet email est déjà utilisé</span>";
        }
    }else{
        $error = true;
        $errorMail = "<span class='text-danger font-italic'>Ce champ est vide ou le mail n'est pas conforme</span>";
    }

     // Validation password
    if(!empty($mdp) || !empty($confirm_mdp))
    {
        if($mdp !== $confirm_mdp){
            $error = true;
            $errorMdp = "<span class='text-danger font-italic'>Les mots de passe ne correspondent pas</span>";
        }
    }else {
        $error = true;
        $errorMdp = "<span class='text-danger font-italic'>Ce champ doit être remplis</span>";
    }

//     // Validation nom
//    if(empty($nom) || strlen($nom) > 20)
//    {
//        $error = true;
//        $errorNom = "<span class='text-danger font-italic'>Ce champ ne peut être vide et doit être inférieur à 20 caractères</span>";
//    }
//
//     // Validation prenom
//    if(empty($prenom) || strlen($prenom) > 20)
//    {
//        $error = true;
//        $errorPrenom = "<span class='text-danger font-italic'>Ce champ ne peut être vide et doit être inférieur à 20 caractères</span>";
//    }
//
//    if($civilite != 'homme' || $civilite != 'femme')
//    {
//        $error = true;
//    }
//
//    if(empty($ville) || strlen($ville) > 20)
//    {
//        $error = true;
//        $errorVille = "<span class='text-danger font-italic'>Ce champ ne peut être vide et doit être inférieur à 20 caractères</span>";
//    }
//
//    if(empty($code_postal) || !is_numeric($code_postal) || strlen($code_postal) != 5)
//    {
//        $error = true;
//        $errorCp = "<span class='text-danger font-italic'>Ce champs doit contenir 5 caractères numériques</span>";
//    }
//
//    if(empty($adresse))
//    {
//        $error = true;
//        $errorAdresse = "<span class='text-danger font-italic'>Ce champ ne peut être vide</span>";
//    }

    if(!$error)
    {
        // cryptage du mdp en BDD
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);

        $insert = $bdd->prepare("INSERT INTO boutique.membre 
            (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) 
            VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse)");
        $insert->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $insert->bindValue(':mdp', $mdp, PDO::PARAM_STR);
        $insert->bindValue(':nom', $nom, PDO::PARAM_STR);
        $insert->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $insert->bindValue(':email', $email, PDO::PARAM_STR);
        $insert->bindValue(':civilite', $civilite, PDO::PARAM_STR);
        $insert->bindValue(':ville', $ville, PDO::PARAM_STR);
        $insert->bindValue(':code_postal', $code_postal, PDO::PARAM_INT);
        $insert->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $insert->execute();

        // redirect after insert member in BDD
        header('location: validation_inscription.php');
    }

    if(isset($errorBd))
    {
        echo $errorBd;
    }

}

require_once 'includes/header.inc.php';
require_once 'includes/nav.inc.php';

?>

<!-- EXO :

    1. Réaliser un formulaire d'inscription correspondant à la table 'membre' de la BDD 'boutique' (sauf id membre) et avec le champ 'confirmer mot de passe' (name="confirm_mdp")

    2. Contrôler en PHP que l'on réceptionne bien toutes les données saisies dans le formulaire

    3. Contrôler la validité du pseudo, si le pseudo est existant en BDD, alors on affiche un message d'erreur. Faites de même pour le champ 'email'

    4. informer l'internaute si les mots de passe ne correspondent pas.

    5. Gérer les failles XSS

    6. SI l'internaute à correctement remplis le formulaire, réaliser le traitement PHP + SQL permettant d'insérer le membre en BDD (requête préparée | prepare() + bindValue())

-->

<form action="" method="post" class="col-md-4 mx-auto pt-4">
    <h2 class="text-center">Inscrivez-vous</h2>
    <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" name="pseudo" id="pseudo" class="form-control">
        <?php if(isset($errorPseudo)) echo $errorPseudo ?>
    </div>
    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" class="form-control">
        <?php if(isset($errorNom)) echo $errorNom ?>
    </div>
    <div class="form-group">
        <label for="prenom">Prenom</label>
        <input type="text" name="prenom" id="prenom" class="form-control">
        <?php if(isset($errorPrenom)) echo $errorPrenom ?>
    </div>
    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="password" name="mdp" id="mdp" class="form-control">
        <?php if(isset($errorMdp)) echo $errorMdp ?>
    </div>
    <div class="form-group">
        <label for="confirm_mdp">Confirmez le mot de passe</label>
        <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-control">
        <?php if(isset($errorMdp)) echo $errorMdp ?>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control">
        <?php if(isset($errorMail)) echo $errorMail ?>
    </div>
    <div class="form-group">
        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" class="form-control">
            <option value="homme">Homme</option>
            <option value="femme">Femme</option>
        </select>
        <?php if(isset($errorCiv)) echo $errorCiv ?>
    </div>
    <div class="form-group">
        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" class="form-control">
        <?php if(isset($errorAdresse)) echo $errorAdresse ?>
    </div>
    <div class="form-group">
        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" class="form-control">
        <?php if(isset($errorVille)) echo $errorVille ?>
    </div>
    <div class="form-group">
        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" class="form-control">
        <?php if(isset($errorCp)) echo $errorCp ?>
    </div>
    <div class="form-group">
        <button class="btn btn-dark mb-5">S'inscrire</button>
    </div>
</form>

<?php require_once 'includes/footer.inc.php'; ?>
