<?php 
require_once"Classes/Conexao.php";
require_once"Classes/CriarAtributos.php";
require_once"Classes/AddClique.php";

// Criando atributos
$zero = 0;
$criador = new CriarAtributos();
$criador->criarAcessoOuToday("acesso", ":mes, :ano, :tipo, $zero");
$criador->criarAcessoOuToday("today", ":dia, :mes, :ano, :tipo, $zero");
$criador->deletarToday("WHERE fkmes!=:mes");
$criador->criarMetas("acesso", "visualizacoes", "WHERE (mes=:mes AND ano=:ano) AND (tipo='facebook' OR tipo='instagram' OR tipo='googleads' OR tipo='browser')");
$criador->criarMetas("acesso", "orcamento", "WHERE mes=:mes AND ano=:ano AND tipo=:tipo");
$criador->criarMetas("acesso", "cadastro", "WHERE mes=:mes AND ano=:ano AND tipo=:tipo");


// Adicionando cliques
if (!isset( $_GET["tipo"]))
	$tipo = "browser";
else
	$tipo = $_GET["tipo"];

$today[0] = new AddClique($tipo);
$cliquesToday = $today[0]->consultarClique("today", "WHERE fktipo=:tipo AND fkmes=:mes AND fkano=:ano AND dia=:dia");
$today[0]->adicionarClique("today", "SET cliques=:newclique WHERE fktipo=:tipo AND fkmes=:mes AND fkano=:ano AND dia=:dia", $cliquesToday[0][0]);

$acesso[0] = new AddClique($tipo);
$cliquesAcesso = $acesso[0]->consultarClique("acesso", "WHERE tipo=:tipo AND mes=:mes AND ano=:ano");
$acesso[0]->adicionarClique("acesso", "SET cliques=:newclique WHERE tipo=:tipo AND mes=:mes AND ano=:ano", $cliquesAcesso[0][0]);

header("Location: home.php");
?>