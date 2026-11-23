
<?php
// conectar os bancos
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sabor&brasa";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>
