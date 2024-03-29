<?php
require_once '../tools/common.php';

if(!isset($_SESSION['is_admin']) OR $_SESSION['is_admin'] == 0){
	header('location:../index.php');
	exit;
}

//Si $_POST['save'] existe, cela signifie que c'est un ajout d'utilisateur
if(isset($_POST['save'])){
	
    $query = $db->prepare('INSERT INTO user (firstname, lastname, password, email, is_admin, bio) VALUES (?, ?, ?, ?, ?, ?)');
    $newUser = $query->execute(
		[
			$_POST['firstname'],
			$_POST['lastname'],
			hash('md5', $_POST['password']),
			$_POST['email'],
			$_POST['is_admin'],
			$_POST['bio'],
		]
    );
	//redirection après enregistrement
	//si $newUser alors l'enregistrement a fonctionné
	if($newUser){ 
		header('location:user-list.php');
		exit;
    }
	else{ //si pas $newUser => enregistrement échoué => générer un message pour l'administrateur à afficher plus bas
		$message = "Impossible d'enregistrer le nouvel utilisateur...";
	}
}

//Si $_POST['update'] existe, cela signifie que c'est une mise à jour d'utilisateur
if(isset($_POST['update'])){

	//début de la chaîne de caractères de la requête de mise à jour
	$queryString = 'UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, bio = :bio ';
	//début du tableau de paramètres de la requête de mise à jour
	$queryParameters = [ 'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'], 'email' => $_POST['email'], 'bio' => $_POST['bio'], 'id' => $_SESSION['user_id'] ];

	//uniquement si l'admin souhaite modifier le mot de passe
	if( !empty($_POST['password'])) {
		//concaténation du champ password à mettre à jour
		$queryString .= ', password = :password ';
		//ajout du paramètre password à mettre à jour
		$queryParameters['password'] = hash('md5', $_POST['password']);
	}
	
	//fin de la chaîne de caractères de la requête de mise à jour
	$queryString .= 'WHERE id = :id';
	
	//préparation et execution de la requête avec la chaîne de caractères et le tableau de données
	$query = $db->prepare($queryString);
	$result = $query->execute($queryParameters);
	
	if($result){
		header('location:user-list.php');
		exit;
	}
	else{
		$message = 'Erreur.';
	}
}

//si on modifie un utilisateur, on doit séléctionner l'utilisateur en question (id envoyé dans URL) pour pré-remplir le formulaire plus bas
if(isset($_GET['user_id']) && isset($_GET['action']) && $_GET['action'] == 'edit'){

	$query = $db->prepare('SELECT * FROM user WHERE id = ?');
    $query->execute(array($_GET['user_id']));
	//$user contiendra les informations de l'utilisateur dont l'id a été envoyé en paramètre d'URL
	$user = $query->fetch();
}

?>

<!DOCTYPE html>
<html>
	<head>

		<title>Administration des utilisateurs - Mon premier blog !</title>

		<?php require 'partials/head_assets.php'; ?>

	</head>
	<body class="index-body">
		<div class="container-fluid">

			<?php require 'partials/header.php'; ?>
		</div>
		<div class="container">
			<div class="row my-3 index-content">


				<section class="col-12">
				
					<header class="pb-4 d-flex justify-content-between">

						<!-- Si $user existe, on affiche "Modifier" SINON on affiche "Ajouter" -->
						<h4><?php if(isset($user)): ?>Modifier<?php else: ?>Ajouter<?php endif; ?> un utilisateur</h4>




						<a class="btn btn-primary" href="user-list.php"><- Liste des utilisateurs</a>
					</header>

					

					<?php if(isset($message)): //si un message a été généré plus haut, l'afficher ?>
					<div class="bg-danger text-white">
						<?php echo $message; ?>
					</div>
					<?php endif; ?>
					
					<!-- Si $user existe, chaque champ du formulaire sera pré-remplit avec les informations de l'utilisateur -->
					
					<form action="user-form.php" method="post">
						<div class="form-group">
							<label for="firstname">Prénom :</label>
							<input class="form-control" <?php if(isset($user)): ?>value="<?php echo $user['firstname']?>"<?php endif; ?> type="text" placeholder="Prénom" name="firstname" id="firstname" />
						</div>
						<div class="form-group">
							<label for="lastname">Nom de famille : </label>
							<input class="form-control" <?php if(isset($user)): ?>value="<?php echo $user['lastname']?>"<?php endif; ?> type="text" placeholder="Nom de famille" name="lastname" id="lastname" />
						</div>
						<div class="form-group">
							<label for="email">Email :</label>
							<input class="form-control" <?php if(isset($user)): ?>value="<?php echo $user['email']?>"<?php endif; ?> type="email" placeholder="Email" name="email" id="email" />
						</div>
						<div class="form-group">
							<label for="password">Password <?php if(isset($user)): ?>(uniquement si vous souhaitez modifier le mot de passe actuel) <?php endif; ?>: </label>
							<input class="form-control" type="password" placeholder="Mot de passe" name="password" id="password" />
						</div>
						<div class="form-group">
							<label for="bio">Biographie :</label>
							<textarea class="form-control" name="bio" id="bio" placeholder="Sa vie son oeuvre..."><?php if(isset($user)): ?><?php echo $user['bio']?><?php endif; ?></textarea>
						</div>
						<div class="form-group">
							<label for="is_admin"> Admin ?</label>
							<select class="form-control" name="is_admin" id="is_admin">
								<option value="0" <?php if(isset($user) && $user['is_admin'] == 0): ?>selected<?php endif; ?>>Non</option>
								<option value="1" <?php if(isset($user) && $user['is_admin'] == 1): ?>selected<?php endif; ?>>Oui</option>
							</select>
						</div>
						
						<div class="text-right">
							<!-- Si $user existe, on affiche un lien de mise à jour -->
							<?php if(isset($user)): ?>
							<input class="btn btn-success" type="submit" name="update" value="Mettre à jour" />
							<!-- Sinon on afficher un lien d'enregistrement d'un nouvel utilisateur -->
							<?php else: ?>
							<input class="btn btn-success" type="submit" name="save" value="Enregistrer" />
							<?php endif; ?>
						</div>

						<!-- Si $user existe, on ajoute un champ caché contenant l'id de l'utilisateur à modifier pour la requête UPDATE -->
						<?php if(isset($user)): ?>
						<input type="hidden" name="id" value="<?php echo $user['id']?>" />
						<?php endif; ?>

					</form>
				</section>
			</div>

		</div>
	</body>
</html>
