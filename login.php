<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_chatt2";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Obter as variáveis de login de forma segura
$email_login = isset($_POST['email_login']) ? $_POST['email_login'] : null;
$senha_login = isset($_POST['senha_login']) ? $_POST['senha_login'] : null;

// Usar prepared statements para evitar injeção de SQL
$stmt = $conn->prepare("SELECT * FROM dados WHERE email = ? AND senha = ?");
$stmt->bind_param("ss", $email_login, $senha_login);

// Executar a consulta
$stmt->execute();

// Obter resultados da consulta
$result = $stmt->get_result();

// Verificar se há uma linha correspondente no banco de dados
if ($result->num_rows > 0) {
    // Conta encontrada
    // Redirecionar para a página de chat
    header("Location: /chatt/client/chat.html");
    exit();
} else {
    // Conta não encontrada
    echo "Conta não encontrada. Verifique suas credenciais e tente novamente.";

    // Aqui você pode adicionar mais lógica, como exibir uma mensagem de erro no formulário de login.
}

// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
