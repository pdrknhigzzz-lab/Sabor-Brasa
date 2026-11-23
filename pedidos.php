
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
include "conexao.php";

// Verificar se o usuário está logado
if (!isset($_SESSION["id"])) {
    die("Você precisa fazer login antes de realizar um pedido!
    <a href='login.html'>Fazer login</a>");
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_SESSION["nome"] ?? "";
    $usuario_id = (int) $_SESSION["id"];

    $produto = $_POST["produto"] ?? "";
    $quantidade = (int) ($_POST["quantidade"] ?? 0);

    if ($nome === "") {
        die("Nome do cliente não encontrado na sessão. Faça login novamente.");
    }

    if ($quantidade < 1 || $quantidade > 20) {
        die("Quantidade inválida!");
    }

    $transacaoIniciada = false;

    try {

        // Buscar produto
        $sql = $conexao->prepare(
            "SELECT id, nome, preco FROM produtos WHERE nome = ?"
        );

        $sql->bind_param("s", $produto);
        $sql->execute();

        $resultado = $sql->get_result();

        if ($resultado->num_rows == 0) {
            $sql->close();
            die("Produto não encontrado no banco de dados!");
        }

        $dados = $resultado->fetch_assoc();
        $sql->close();

        $produto_id = (int) $dados["id"];
        $preco = (float) $dados["preco"];

        $total = $preco * $quantidade;

        // Iniciar transação
        $conexao->begin_transaction();
        $transacaoIniciada = true;

        // Registrar pedido
        $pedido = $conexao->prepare(
            "INSERT INTO pedidos (usuario_id, nome, total)
             VALUES (?, ?, ?)"
        );

        $pedido->bind_param("isd", $usuario_id, $nome, $total);
        $pedido->execute();

        $pedido_id = $conexao->insert_id;

        // Registrar item do pedido
        $item = $conexao->prepare(
            "INSERT INTO itens_pedido
            (pedido_id, produto_id, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?)"
        );

        $item->bind_param(
            "iiid",
            $pedido_id,
            $produto_id,
            $quantidade,
            $preco
        );

        $item->execute();

        // Confirmar gravação
        $conexao->commit();
        $transacaoIniciada = false;

        echo "<h2>Pedido realizado com sucesso!</h2>";

        echo "<p>Cliente: " .
             htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') .
             "</p>";

        echo "<p>Produto: " .
             htmlspecialchars($dados["nome"], ENT_QUOTES, 'UTF-8') .
             "</p>";

        echo "<p>Quantidade: " . $quantidade . "</p>";

        echo "<p>Preço unitário: R$ " .
             number_format($preco, 2, ',', '.') .
             "</p>";

        echo "<h3>Total: R$ " .
             number_format($total, 2, ',', '.') .
             "</h3>";

        echo "<a href='index.html'>Voltar ao site</a>";

        $pedido->close();
        $item->close();

    } catch (Throwable $erro) {

        if ($transacaoIniciada) {
            $conexao->rollback();
        }

        echo "<h2>Erro ao realizar o pedido</h2>";
        echo "<pre>";
        echo htmlspecialchars(
            $erro->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        );
        echo "</pre>";

    }

} else {

    echo "Nenhum pedido foi enviado!";

}

?>


