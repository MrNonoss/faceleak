<?php
$alerting = Array();

// Fonction de connexion à la base de données
function connectMe() {
	$message = false;
	$bdd = false;
	try {
		$bdd = new PDO('pgsql:host=postgre;dbname=faceleak', 'example', 'postgres');
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		$message = 'Exception BDD : '. $e->getMessage();
	}
	
	return Array($bdd,$message);
}

// Fonction de sécurisation des entrées utilisateurs
function securize($p) {
	$v = htmlspecialchars($p); //Convertir les caractères spéciaux
    $v = trim($v); //Supprimer les espaces dans la requête
    $v = rtrim($v); //Supprimer les espaces à la fin de la requête
    $v = strtolower($v); //Tout mettre en minuscule
    $v = strip_tags($v); //Supprimer les balises html dans la requête
    $v = stripslashes($v); //Supprimer les slash dans la requête
    $v = stripcslashes($v); //Supprimer les backslash dans la requête
	
	return $v;
}

// Choix de la base à requêter
$table = (isset($_GET["base"])) ? $_GET['base']:'france';

//Déclaration et initialisation de la variable de recherche
$term = '';

// Boucle de requêtes
if (isset($_GET["search"])) {
	$term = securize($_GET['search']);	
	if (!empty($term)) {
		$connector = connectMe();		
		if ($connector[0] !== false AND $_GET["criteria"] == "Nom") {
			$select = $connector[0]->prepare("SELECT * FROM $table WHERE name LIKE ? OR surname LIKE ?");
			$select->execute(Array("%".$term."%", "%".$term."%"));
			$list = $select->fetchAll();
        } elseif ($connector[0] !== false AND $_GET["criteria"] == "Telephone") {
			$select = $connector[0]->prepare("SELECT * FROM $table WHERE phone LIKE ?");
			$select->execute(Array("%".$term."%"));
			$list = $select->fetchAll();
        } elseif ($connector[0] !== false AND $_GET["criteria"] == "Identifiant") {
			$select = $connector[0]->prepare("SELECT * FROM $table WHERE id LIKE ?");
			$select->execute(Array("%".$term."%"));
			$list = $select->fetchAll();
		} else {
			$alerting[] = $connector[1];
		}
	} else {
		$alerting[] = 'La nature a horreur du vide';
	}
}
?>
<!-- Formulaire HTML -->
<!DOCTYPE html>
<html>
   <head>
      <meta charset = "utf-8" >
      <title>Facebook Leak Search</title>
	  <style>
			body{
                color: #000;
                font-family: "Century Gothic", helvetica, arial, sans-serif;}
			input {
                font-family: "Century Gothic", helvetica, arial, sans-serif;
                padding:1px;
                font-size:1.3em;
                margin:3px;}
			table{border:2px solid #000;  border-collapse: collapse; border-spacing: 0;}
			td, th{border:1px solid #000;padding:5px;}
			.info {margin:5px;color:#444;font-style:italic;}
			.alerting {margin:5px;color:red;font-weight:bold;}
		</style>
   </head>
   <body>
    <h1 style="color:blue";>Facebook Leak Search</h1>
      <form action = "/" method = "GET">
        <input type="search" placeholder="Le savoir n'attend pas..." name="search" value="<?php echo $term?>" size="27">
        <input type="radio" name="base" value="france" checked>
        <label for="france">France</label>
        <input type="radio" name="base" value="monde">
        <label for="monde">Monde</label><br>
        <input type="submit" name="criteria" value="Nom">
        <input type="submit" name="criteria" value="Telephone">
        <input type="submit" name="criteria" value="Identifiant">
      </form>
	  <hr/>
      <hr/>

      <?php
	  if (isset($list)) {
		  if (count($list) > 0) {
				$result = '<table>';
				$result .= '<tr><th>Prénom</th><th>Nom</th><th>Téléphone</th><th>Identifiant</th></tr>';			
				foreach($list as $value) {
					$result .= '<tr><td>'.$value['name'].'</td><td>'.$value['surname'].'</td><td>'.$value['phone'].'</td><td><a href="'.$value['id'].'">'.$value['id'].'</a></td></tr>';
				}
				$result .= '</table>';
				echo $result;
		  } else {
			echo '<div class="info">Pas de résultat sur ce coup là</div>';
		  }
	  }
	  
	  if (is_countable($alerting > 0)) {
			$result ='';
			foreach($alerting as $value) {
				$result .= '<div class="alerting">'.$value.'</div>';
			}
			echo $result;
	  }
      ?>
   </body>
</html>