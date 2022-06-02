<?php
/* Aqui programaremos futuramente
os recursos de login/logout e verificação
de permissão de acesso dos usuários */

/* verificarndo se nao existe se nao existe uma sessao em funcionamento */

if (!isset ($_SESSION)){
    session_start();
}

function verificaAcesso(){
    /* se nao existir uma variavel de sessao */
    if (!isset ($_SESSION['id'])){
        session_destroy();
        header("location:../login.php"); die();
    }
}

function login(int $id, string $nome, string $email, string $tipo){
    /* variaveis de sessao ao logar */
    $_SESSION['id'] = $id;
    $_SESSION['nome'] = $nome;
    $_SESSION['email'] = $email;
    $_SESSION['tipo'] = $tipo;

}

function logout(){
    session_start();
    session_destroy();
    header("location:../login.php?logout");
    die();
}
