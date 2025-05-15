<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>testeeee</title>
</head>
<body>
    
<?php
        //receber os dados do formulário
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        //Acessar o if quando o usuário clicar no botão acessar do formulário
        if(!empty($dados['Sendlogin'])){
            var_dump($dados);
        }

?>

<!-- inicio do formulario de login -->
    <form method="POST" action="">
    <label>Usuários: </label>
    <input type="text" name="usuário" placeholder="Digite o usuário"><br></br>

    <label>Senha: </label>
    <input type="password" name="senha_usuario" placeholder="Digite a senha"><br></br>

    </form>


</body>
</html>
