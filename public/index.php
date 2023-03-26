<?php
define('MAX_LENGTH_LASTNAME', 45);
define('MAX_LENGTH_FIRSTNAME', 45);

require_once '_connec.php';
$pdo = new PDO(DSN, USER, PASS);

//  INITIALISE VARIABLES
$lastname = $firstname = "";
$errors = [];

//  CHECK POST METHOD ONLY
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //  SANITIZE POST DATA
    if (!empty($_POST)) {
        //suppression des espaces
        $datas = array_map('trim', $_POST);
        var_dump($datas);
        $lastname = $datas['lastname'];
        $firstname = $datas['firstname'];

        // nettoyage et validation des données soumises via le formulaire
        if (empty($firstname)) {
            $errors[] = "Le prénom est obligatoire";
        }
        if (empty($lastname)) {
            $errors[] = "Le nom est obligatoire";
        }

        if (strlen($firstname) > MAX_LENGTH_FIRSTNAME) {
            $errors[] = "La zone prénom doit faire une longeur de " . MAX_LENGTH_FIRSTNAME . " caractères ";
        }
        if (strlen($lastname) > MAX_LENGTH_LASTNAME) {
            $errors[] = "La zone nom doit faire une longeur de " . MAX_LENGTH_LASTNAME . " caractères";
        }

        if (count($errors) === 0) {

            // On prépare notre requête d'insertion
            $query = 'INSERT INTO friend (firstname, lastname) VALUES (:firstname, :lastname)';
            $statement = $pdo->prepare($query);
            // On lie les valeurs saisies dans le formulaire à nos placeholders
            $statement->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
            $statement->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
            $statement->execute();
            //$nbrLigneInserted = $statement->rowCount();

            header("Location: index.php");
        }
    }
}

// A exécuter afin de tester le contenu de votre table friend
$query = "SELECT * FROM friend";
$statement = $pdo->query($query);
// On veut afficher notre résultat via un tableau associatif (PDO::FETCH_OBJ)
$friendsObject = $statement->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <title>Pdo exercice</title>
</head>

<body>

    <section class="form-container">

        <div class="mess-warning <?= (count($errors) === 0) ? 'hide' : 'show'; ?>">
            <ul>
                <?php if (count($errors) > 0) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                            <?= $error ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <legend>
                <h2>Contact Form</h2>
            </legend>
            <div>
                <label for="lastname">Nom :</label>
                <input type="text" id="lastname" name="lastname" value="<?= htmlentities($lastname); ?>" maxlength="<?= MAX_LENGTH_LASTNAME ?>" required>
            </div>
            <div>
                <label for="firstname">Prenom :</label>
                <input type="text" id="firstname" name="firstname" value="<?= htmlentities($firstname); ?>" maxlength="<?= MAX_LENGTH_FIRSTNAME ?>" required>
            </div>
            <div>
                <button type="submit" class="btn submit">Envoyer</button>
            </div>
        </form>

    </section>

    <section class="datas-container">
        <h2>Données de la table friend</h2>
        <ul>
            <?php foreach ($friendsObject as $friend) : ?>
                <li><?= $friend->firstname . ' ' . $friend->lastname ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

</body>

</html>