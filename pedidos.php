
<?php

session_start();
include "conexao.php";

// Verificar se o usuário está logado
if (!isset($_SESSION["id"])) {
    die("Você precisa fazer login antes de realizar um pedido!
    <a href='login.html'>Fazer login</a>");
}

// Verificar se os dados foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_SESSION["nome"];
    $usuario_id = $_SESSION["id"];
    $produto = $_POST["produto"];
    $quantidade = (int) $_POST["quantidade"];

    // Verificar a quantidade
    if ($quantidade < 1 || $quantidade > 20) {
        die("Quantidade inválida!");
    }

    // Buscar o produto no banco
    $sql = $conexao->prepare(
        "SELECT id, nome, preco FROM produtos WHERE nome = ?"
    );

    $sql->bind_param("s", $produto);
    $sql->execute();

    $resultado = $sql->get_result();

    if ($resultado->num_rows == 0) {
        die("Produto não encontrado!");
    }

    $dados = $resultado->fetch_assoc();
    
    $produto_id = $dados["id"];
    $preco = $dados["preco"];

    // Calcular o valor total
    $total = $preco * $quantidade;

    // Iniciar transação
    $conexao->begin_transaction();

    try {

        // Inserir pedido
$pedido = $conexao->prepare(
    "INSERT INTO pedidos (usuario_id, nome, total)
     VALUES (?, ?, ?)"
);

$pedido->bind_param("isd", $usuario_id, $nome, $total);

$pedido->execute();

// Pegar o ID do pedido
$pedido_id = $conexao->insert_id;

        // Inserir item do pedido
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

        // Salvar no banco
        $conexao->commit();

        // Mostrar confirmação
        echo "<h2>Pedido realizado com sucesso!</h2>";

        echo "<p>Cliente: " .
        htmlspecialchars($_SESSION["nome"]) . "</p>";

        echo "<p>Hambúrguer: " .
        htmlspecialchars($dados["nome"]) . "</p>";

        echo "<p>Quantidade: " . $quantidade . "</p>";

        echo "<p>Preço unitário: R$ " .
        number_format($preco, 2, ',', '.') . "</p>";

        echo "<h3>Total: R$ " .
        number_format($total, 2, ',', '.') . "</h3>";

        echo "<a href='index.html'>Voltar ao site</a>";

        $pedido->close();
        $item->close();

    } catch (Exception $erro) {

        $conexao->rollback();

        echo "Erro ao realizar o pedido!";

    }

    $sql->close();

} else {

    echo "Nenhum pedido foi enviado!";

}

?>
