<?php 
include_once"criarAtributo.php";
require_once"Classes/AddClique.php";
$add_clique = new AddClique();
$add_clique->setTipo("facebook");
$add_clique->insertClique();
header("Location: home.php");
?>