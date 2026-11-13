<?php
session_start();


include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = $conexao->prepare(
        "SELECT * FROM usuarios WHERE email = ?"
    );

    $sql->bind_param("s", $email);
    $sql->execute();

    $resultado = $sql->get_result();

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha"])) {

            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["id"] = $usuario["id"];

            header("Location: index.html");
            exit;

        } else {

            echo "Senha incorreta!";

        }

    } else {

        echo "E-mail não cadastrado!";

    }
}

$_SESSION["nome"] = $usuario["nome"];
$_SESSION["id"] = $usuario["id"];
?>