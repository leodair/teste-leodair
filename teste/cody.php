<?php
// exemplo criptografar a senha
// echo password_hash(123456, PASSWORD_DEFAULT);

// Receber os dados do formulário
$_dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessar o IF quando o usuário clicar no botão acessar do formulário
if (!empty($_dados['SendLogin'])) {
    //var_dump($_dados);

    // Recuperar os dados do usuário no banco de dados
    $query_usuario = "SELECT id, nome, usuario, senha_usuario FROM usuarios WHERE usuario = :usuario LIMIT 1";

    // Preparar a QUERY
    $result_usuario = $conn->prepare($query_usuario);

    // Substituir o link da query pelo valor que vem do formulário
    $result_usuario->bindParam(':usuario', $_dados['usuario']);

    // Executar a QUERY
    $result_usuario->execute();

    // Acessar o IF quando encontrar usuário no banco de dados
    if (($result_usuario) && ($result_usuario->rowCount() != 0)) {
        // Ler os registros retornando do banco de dados
        $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);
        //var_dump($row_usuario);

        // Acessar o IF quando a senha é válida
        if (password_verify($_dados['senha_usuario'], $row_usuario['senha_usuario'])) {
            // Salvar os dados do usuário na sessão
            $_SESSION['id'] = $row_usuario['id'];
            $_SESSION['usuario'] = $row_usuario['usuario'];

            // Recuperar a data atual
            $data = date('Y-m-d H:i:s');

            // Gerar número randômico entre 100000 e 999999
            $codigo_autenticacao = mt_rand(100000, 999999);
            //var_dump($codigo_autenticacao);

            // QUERY para salvar no banco de dados o código e a data gerada
            $query_up_usuario = "UPDATE usuarios SET codigo_autenticacao = :codigo_autenticacao, data_codigo_autenticacao = :data_codigo_autenticacao WHERE id = :id LIMIT 1";
            // As próximas linhas para preparar e executar a query de atualização não estão na imagem.
        }
    }
}

// Preparar a QUERY
$result_up_usuario = $conn->prepare($query_up_usuario);

// Substituir o link da QUERY pelos valores
$result_up_usuario->bindParam(':codigo_autenticacao', $codigo_autenticacao);
$result_up_usuario->bindParam(':data_codigo_autenticacao', $data);
$result_up_usuario->bindParam(':id', $row_usuario['id']);

// Executar a QUERY
$result_up_usuario->execute();

// Incluir o Composer
require './lib/vendor/autoload.php';

// Criar o objeto e instanciar a classe do PHPMailer
$mail = new PHPMailer(true);

// Verificar se envia o e-mail corretamente com try catch
try {
    // Imprimir os erro com debug
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

// Permitir o envio do e-mail com caracteres especiais
$mail->CharSet = 'UTF-8';

// Definir para usar SMTP
$mail->isSMTP();

// Servidor de envio de e-mail
$mail->Host = 'smtp.servidor_enviar_email'; // Insira o seu servidor SMTP

// Indicar que é necessário autenticar
$mail->SMTPAuth = true;

// Usuário/o e-mail para enviar o e-mail
$mail->Username = 'email_remetente@dominio.com.br'; // Seu e-mail de envio

// Senha do e-mail utilizado para enviar e-mail
$mail->Password = 'senha_email_remetente'; // Sua senha de e-mail

// Ativar criptografia
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

// Porta para enviar e-mail
$mail->Port = 587;

// E-mail do remetente
$mail->setFrom('atendimento@seuemail.com.br', 'Atendimento'); // Seu e-mail e nome

// E-mail de destino
$mail->addAddress($row_usuario['usuario'], $row_usuario['nome']);

// Definir formato do e-mail para HTML
$mail->isHTML(true);

// Título do e-mail
$mail->Subject = 'Aqui está o código de verificação de 6 dígitos que você solicitou';

// Conteúdo do e-mail em formato HTML
$mail->Body    = 'Olá <b>' . $row_usuario['nome'] . '</b>,<br><br>Autenticação Multifator:<br>Seu código de verificação de 6 dígitos é:<br><b>' . $codigo_autenticacao . '</b><br>Esse código foi enviado para verificar seu login.<br><br>';

// Conteúdo do e-mail em formato texto
$mail->AltBody = 'Olá ' . $row_usuario['nome'] . ',\n\nAutenticação Multifator:\nSeu código de verificação de 6 dígitos é: ' . $codigo_autenticacao . '\nEsse código foi enviado para verificar seu login.\n\n';

// Enviar o e-mail
$mail->send();

// Redirecionar o usuário
header("Location: validar_codigo.php");

} catch (Exception $e) {
// Acessa o catch quando não é enviado e-mail corretamente
echo "E-mail não enviado. Erro: {$mail->ErrorInfo}";
$_SESSION['msg'] = "<p style='color: #f00;'>Erro: E-mail não enviado com sucesso!</p>";
}
else {
$_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário ou Senha Inválida!</p>";
} else {
$_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário ou Senha Inválida!</p>";
}
// Imprimir a mensagem da sessão
if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}