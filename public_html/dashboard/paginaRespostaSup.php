<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>EZY 3D - Dasboard</title>

	<!-- Custom fonts for this template-->
	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">
		<?php
		include "menu.php";
		if (!($permissao == "@571824" || $permissao == "&43642")) {
			session_destroy();
			header("Location: ../pa-admin.php");
		}
		?>
		<!-- Begin Page Content -->
		<div class="container-fluid">

			<h1 class="h3 mb-2 text-gray-800">
				<a href="suporte.php"><i class="fas fa-long-arrow-alt-left"></i></a>
			</h1>

			<h1 class="h5 mb-4 text-gray-800">
				<a href="suporte.php">Suporte</a> <i class="fas fa-chevron-right mr-2 ml-2"></i>
				Resposta
			</h1>

			<!-- Page Heading -->
			<h1 class="h3 mb-4 text-gray-800">Resposta</h1>

			<div class="card">
				<?php
				require_once "../Classes/Conexao.php";
				require_once "../Classes/Usuario.php";
				$id = $_GET["id"];
				$fk = $_GET["fk"];
				$obj = new Usuario();
				$registru = $obj->consultar("*", "cliente", "where idcliente = " . $fk, null);
				foreach ($registru as $clienti) { ?>
					<div class="card-header">
						<h2><?= $clienti["nome"] ?></h2>
						</div> <?php } ?>
						<?php
						$registro = $obj->consultar("*", "suporte", "where idsuporte = " . $id, null);
						foreach ($registro as $cliente) {
							?>
							<div class="card-body">
								<blockquote class="blockquote mb-0">
									<label style="font-weight: bold">Produto: </label>
									<p><?= $cliente['maquina'] ?></p>
									<label style="font-weight: bold">Descrição: </label>
									<p><?= $cliente['problema'] ?></p>
									<label style="font-weight: bold">Data: </label>
									<p><?= $cliente['descricao'] ?></p>
								</blockquote>
							</div>
						</div> <?php } ?><br>

						<div class="card">
							<div class="card-body">
								<form action="resposta.php?id=<?= $id ?>&tp=sup" method="post" class="col-md-12">
									<?php
									$obj = new Usuario();
									$fk = $_GET["fk"];
									$registro = $obj->consultar("*", "cliente", "where idcliente = " . $fk, null);
									foreach ($registro as $cliente) { ?>
										<input hidden type="text" class="form-control" name="assunto" value="Resposta"> <br>
										<input hidden name="destinatario" type="email" class="form-control" name="email" value="<?= $cliente["email"] ?>">
										<textarea class="form-control" placeholder="resposta" name="resposta" rows="4"></textarea><br>
										<input type="text" name="status" value="respondido" hidden>
										<input type="reset" value="Reset" class="btn btn-danger">
										<input type="submit" value="Enviar" class="btn btn-primary">
									<?php } ?>
								</form>
							</div>
						</div>

					</div>
					<!-- /.container-fluid -->

				</div>
				<!-- End of Main Content -->

				<?php
				include_once "rodape.php";
				?>


			</body>

			</html>