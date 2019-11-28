<?php

require_once 'tools/common.php';

//si un utilisateur est connécté et que l'on reçoit le paramètre "lougout" via URL, on le déconnecte

if(isset($_GET['logout']) && isset($_SESSION['user'])){

	//la fonction unset() détruit une variable ou une partie de tableau. ici on détruit la session user
	unset($_SESSION["user"]);
	//détruire $_SESSION["user"] va permettre l'affichage du bouton connexion / inscription de la nav, et permettre à nouveau l'accès aux formulaires de connexion / inscription
	//détruire $_SESSION["is_admin"] va empêcher l'accès au back-office
	unset($_SESSION["is_admin"]);
	unset($_SESSION["user_id"]);
}

?>

<!DOCTYPE html>
<html>
	<head>

		<title>Homepage - Mon premier blog !</title>

		<?php require 'partials/head_assets.php'; ?>

	</head>
	<body class="index-body">
		<div class="container-fluid">

			<?php require 'partials/header.php'; ?>

			</div>
						

						



	<!-- Si une session utilisateur existe (utilisateur connécté) on affiche son prénom et un boutton pour se déconnecter -->
	<?php if(isset($_SESSION['user'])): ?>
	<p class="h2 text-center">Salut <?php echo $_SESSION['user']; ?> !</p>
	<!-- ici le boutton de déconnexion est un lien allant vers l'index qui envoie le paramètre "logout" via URL -->
	<p>
	<div class="container">
			<div class="col-12">
				<a class="d-block btn btn-danger mb-4 mt-2" href="index.php?logout">Déconnexion</a>
		
				<?php if($_SESSION['is_admin'] == 1): ?>
				<a class="d-block btn btn-warning mb-4 mt-2" href="admin/user-list.php">Administration</a>
				<?php else: ?>
			</div>
		</div>
		
		<a class="d-block btn btn-warning mb-4 mt-2" href="user-profile.php">Profile</a>
		<?php endif; ?>
	</p>
	<?php else: ?>
	<!-- Sinon afficher un boutton de connexion -->
		<div class="container">
			<div class="col-12">
				<a class="d-block btn btn-primary ml-4 mt-2" href="login-register.php">Connexion / inscription</a>
			</div>
		</div>
	
	
	<?php endif; ?>

	
			</div>
			
	</body>
</html>
