<?php
// Conectar ao banco de dados (substitua os valores conforme necessário)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_chatt2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recuperar dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['password1'];

// Verificar se o e-mail já existe no banco de dados
$checkEmailStmt = $conn->prepare("SELECT * FROM dados WHERE email = ?");
$checkEmailStmt->bind_param("s", $email);
$checkEmailStmt->execute();
$checkEmailResult = $checkEmailStmt->get_result();

if ($checkEmailResult->num_rows > 0) {
    // E-mail já cadastrado, emitir um alerta
    echo "<script>alert('E-mail já cadastrado. Tente novamente com um e-mail diferente.');</script>";
} else {
    // E-mail não cadastrado, proceder com a inserção
    $insertStmt = $conn->prepare("INSERT INTO dados (nome, email, senha) VALUES (?, ?, ?)");
    $insertStmt->bind_param("sss", $nome, $email, $senha);

    if ($insertStmt->execute()) {
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados: " . $insertStmt->error;
    }

    // Fechar a conexão com o banco de dados
    $insertStmt->close();
}

// Fechar a conexão com o banco de dados
$checkEmailStmt->close();
$conn->close();
?>
