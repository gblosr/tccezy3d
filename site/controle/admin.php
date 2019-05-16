<?php 
error_reporting(0);
ini_set(“display_errors”, 0 );
if(!isset($_POST["tipo"]))
	header("Location: home.php");

require_once"../Classes/Conexao.php";
require_once"../Classes/Usuario.php";
extract($_POST);
$cliente = new Usuario();

// Verificando se já existe email, via AJAX antes do submit do formulário cadastrar
if($tipo == "verificarEmail"){
	session_start();
	if(isset($_SESSION["idcliente"])){
		$idcliente = $_SESSION["idcliente"];
		$dados = ["idcliente" => $idcliente, "email" => $email];
		$where = "WHERE idcliente!=:idcliente AND email=:email";
	}
	else{
		$dados = ["email" => $email];
		$where = "WHERE email=:email";
	}
	$qtdEmail = $cliente->consultar("count(*)", $where, $dados);
	echo json_encode($qtdEmail, JSON_UNESCAPED_UNICODE);
}
// Cadastro
else if ($tipo == "cadastro"){
	$valor = ["email" => $email];
	$qtdEmail = $cliente->consultar("count(*)", "WHERE email=:email", $valor);
	if($qtdEmail[0][0] != 0)
		header("Location: ../form_cadastrar.php?error=true");

	else{
		if($ramo == "outro")
				$ramo = $outroRamo;

		if (strlen($nome) < 3 || strlen($sobrenome) < 3 || strlen($telefone) != 11 || strlen($senha) < 8 || strlen($confirmarSenha) < 8 || ($pais == "null" || strlen($pais) == 0) || ($ramo == "null" || strlen($ramo) < 3))
			header("Location:../home.php");
		else{	
			$senhaCriptografada = sha1($senha);
			$dados = ["cod" => 0, "nome" => $nome,"sobrenome" => $sobrenome, "email" => $email,  "senha" => $senhaCriptografada, "telefone" => $telefone, "pais" => $pais, "ramo" => $ramo, "empresa" => $empresa];
			$cliente->cadastrar("cliente", ":cod, :nome, :sobrenome, :email, :senha, :telefone, :pais, :ramo, :empresa", $dados);
			header("Location: ../index.php?tipo=cadastro&email=$email");
		}
	}
}

// Verificando se login está correto, via AJAX antes do submit do formulário de login
else if($tipo == "verificarLogin"){
	$dados = ["email" => $email, "senha" => sha1($senha)];
	$qtdLogin = $cliente->consultar("count(*)", "WHERE email=:email AND senha=:senha", $dados);
	echo json_encode($qtdLogin, JSON_UNESCAPED_UNICODE);
}
// Login
else if($tipo == "login"){
	$dados = ["email" => $email_login];
	$logon = $cliente->consultar("*", "WHERE email=:email", $dados);
	if($logon[0]["senha"] === sha1($senha_login)){
		$cliente->criarSession($logon[0]["idcliente"], $logon[0]["nome"], $logon[0]["sobrenome"], $logon[0]["email"],  $logon[0]["senha"], $logon[0]["telefone"], $logon[0]["pais"], $logon[0]["ramo"], $logon[0]["empresa"]);
		header("Location: ../perfil_cliente.php");
	}
	else
		echo "<script>alert('Senha ou email incorreto(s)');window.location.href='../home.php';</script>";	
}

// Exclusão de cliente, disponivel apenas no painel administrativo para o admin 'Master'
else if($tipo == "excluir"){
	$dados = ["id" => $id];
	$cliente->excluir("cliente", "WHERE idcliente=:id", $dados);
}


else if($tipo == "consultar"){
	$dados = ["id" => $id];
	$retorno = $cliente->consultar("*", "WHERE idcliente=:id", $dados);
	echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
}

// Convertendo valores do input 'senha' e 'confirmarSenha' em HASH (SHA1) e retornando-o via AJAX
else if($tipo == "consultarSenha"){
	$senhaold = ["senha" => sha1($inputSenha), "novasenha" => sha1($inputNovaSenha)];
	echo json_encode($senhaold, JSON_UNESCAPED_UNICODE);
}

// Alterar informações, disponivel para o cliente
else if($tipo == "alterar"){
	session_start();
	if(!isset($_SESSION["idcliente"]))
		header("Location: ../home.php");
	else
		$idcliente = $_SESSION["idcliente"];

	$valor = ["idcliente" => $idcliente, "email" => $email];
	$qtdEmail = $cliente->consultar("count(*)", "WHERE idcliente!=:idcliente AND email=:email", $valor);
	if($qtdEmail[0][0] != 0)
		header("Location: ../form_cadastrar.php?error=true");

	else{
		if($ramo == "outro")
			$ramo = $outroRamo;

		if (strlen($nome) < 3 || strlen($sobrenome) < 3 || strlen($telefone) != 11 || strlen($pais) < 2 || strlen($ramo) < 3)
			header("Location:../home.php");
		else{
			$dados1 = ["nome" => $nome, "sobrenome" => $sobrenome, "email" => $email, "telefone" => $telefone, "pais" => $pais, "ramo" => $ramo, "empresa" => $empresa, "idcliente" => $idcliente];
			$cliente->alterar("cliente", "nome=:nome, sobrenome=:sobrenome, email=:email, telefone=:telefone, pais=:pais, ramo=:ramo, empresa=:empresa WHERE idcliente=:idcliente", $dados1);

	// Atualizando os dados do SESSION
			$dados2 = ["idcliente" => $idcliente];
			$logon = $cliente->consultar("*", "WHERE idcliente=:idcliente", $dados2);
			$cliente->criarSession($logon[0]["idcliente"], $logon[0]["nome"], $logon[0]["sobrenome"], $logon[0]["email"], $logon[0]["senha"], $logon[0]["telefone"], $logon[0]["pais"], $logon[0]["ramo"], $logon[0]["empresa"]);

			header("Location:../perfil_cliente.php?status=success1");
		}
	}
}

// Alterar senha, disponivel para o cliente
else if($tipo == "alterar-senha"){
	session_start();
	$idcliente = $_SESSION["idcliente"];
	$dados = ["novasenha" => sha1($novasenha), "idcliente" => $idcliente];
	$cliente->alterar("cliente", "senha=:novasenha WHERE idcliente=:idcliente", $dados);

	$dados2 = ["idcliente" => $idcliente];
	$logon = $cliente->consultar("*", "WHERE idcliente=:idcliente", $dados2);
	$cliente->criarSession($logon[0]["idcliente"], $logon[0]["nome"], $logon[0]["sobrenome"], $logon[0]["email"], $logon[0]["senha"], $logon[0]["telefone"], $logon[0]["pais"], $logon[0]["ramo"], $logon[0]["empresa"]);

	header("Location:../perfil_cliente.php?status=success2");
}

// Orçamento, disponivel para o cliente
else if($tipo == "orcamento"){
	if(strlen($descricao) == 0){
		$_POST["descricao"] = "null";
		$descricao = $_POST["descricao"];
	}

	$agrupar = [];
	foreach ($_POST as $value) {
		if($value == strstr($value, "produto")){
			$partes = explode("_", $value);
			$nome_produto = $partes[1];
			$agrupar[] .= $nome_produto;
		}	
	}
	// verificação de preenchimento dos checkboxs
	if(count($agrupar) == 0)
		header("Location:../perfil_cliente.php");

	$produtos = implode(', ', $agrupar);
	$dados = ["idorcamento" => 0, "fkcliente" => $idcliente, "produtos" => $produtos, "descricao" => $descricao, "data" => date("Y-m-d H:i:s")];
	$cliente->cadastrar("orcamento", ":idorcamento, :fkcliente, :produtos, :descricao, :data", $dados);
	header("Location: ../index.php?tipo=orcamento");
}

// Suporte Técnico, disponivel para o cliente
else if($tipo == "suporte"){
	if(($maquina == "null" || strlen($maquina) == 0) || ($problema == "null" || strlen($problema) == 0))
		header("Location: ../perfil_cliente.php");

	else{
		session_start();
		$fkcliente = $_SESSION["idcliente"];
		$dados = ["idsuporte" => 0, "maquina" => $maquina, "problema" => $problema, "descricao" => $descricao, "fkcliente" => $fkcliente];
		$cliente->cadastrar("suporte", ":idsuporte, :maquina, :problema, :descricao, :fkcliente", $dados);
		header("Location: ../index.php?tipo=suporte");
	}
}


else
	header("Location:../home.php");
?>