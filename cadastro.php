

<?php

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    if ($senha != $confirmar_senha) {
        die("As senhas não coincidem!");
    }

    // Verificar e-mail
    $verificar = $conexao->prepare(
        "SELECT id FROM usuarios WHERE email = ?"
    );

    $verificar->bind_param("s", $email);
    $verificar->execute();

    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        die("E-mail já cadastrado!");
    }

    // Criptografar senha
    $senha_protegida = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    // Inserir usuário
    $stmt = $conexao->prepare(
        "INSERT INTO usuarios (nome, email, senha)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "sss",
        $nome,
        $email,
        $senha_protegida
    );

    if ($stmt->execute()) {

        echo "Cadastro realizado com sucesso!";
        echo "<br><a href='login.html'>Fazer login</a>";

    } else {

        echo "Erro ao cadastrar!";

    }

    $stmt->close();
    $verificar->close();

}

?>

