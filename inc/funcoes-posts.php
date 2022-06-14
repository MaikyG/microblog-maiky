<?php
require "conecta.php";

/* Usada em post-insere.php */
function inserirPost(mysqli $conexao, string $titulo, string $texto, string $resumo, $imagem, int $idUsuarioLogado){
    $sql = "INSERT INTO posts(titulo, texto, resumo, imagem, usuario_id) VALUES('$titulo','$texto','$resumo','$imagem','$idUsuarioLogado')";
    
    mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
} // fim inserirPost



/* Usada em posts.php */
function lerPosts(mysqli $conexao, int $idUsuarioLogado, string $tipoUsuarioLogado):array {
    //seo tipo de usuario for admin
    if ($tipoUsuarioLogado == 'admin') {
        //sql que traga todos os posts
        $sql = "SELECT posts.id, posts.titulo, posts.data, usuarios.nome AS autor FROM posts INNER JOIN usuarios ON posts.usuario_id = usuarios.id ORDER BY data DESC";
    } else {
        //senão, sql que traga os posts do editor
        $sql = "SELECT id, titulo, data FROM posts WHERE usuario_id = $idUsuarioLogado ORDER BY data DESC";
    };
    

    $resultado = mysqli_query($conexao,$sql) or die(mysqli_error($conexao));
    $posts = [];
    while($post = mysqli_fetch_assoc($resultado)){
        array_push($posts, $post);
    }
    return $posts;
} // fim lerPosts


/* Usada em post-atualiza.php */
function lerUmPost(mysqli $conexao, int $idPost, int $idUsuarioLogado, string $tipoUsuarioLogado):array {    
/* se o usuario logado for admin, entao pode carregar os dados de qualquer post de qualquer usuario, caso contrario so pode carregar os dados dos proprios posts */

    if ($tipoUsuarioLogado == 'admin') {
        $sql ="SELECT id, titulo, texto, resumo, imagem, usuario_id FROM posts WHERE id = $idPost";
    } else {
        $sql ="SELECT titulo, texto, resumo, imagem, usuario_id FROM posts WHERE id = $idPost AND usuario_id = $idUsuarioLogado";
    }
	$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    return mysqli_fetch_assoc($resultado); 
} // fim lerUmPost



/* Usada em post-atualiza.php */
function atualizarPost(mysqli $conexao, int $idPost, int $idUsuarioLogado, string $tipoUsuarioLogado, string $titulo, string $texto, string $resumo, string $imagem){
    
    if ($tipoUsuarioLogado == 'admin') {
        $sql = "UPDATE posts SET titulo='$titulo', texto='$texto', resumo='$resumo', imagem='$imagem' WHERE id= $idPost";
    } else {
        $sql = "UPDATE posts SET titulo='$titulo', texto='$texto', resumo='$resumo', imagem='$imagem' WHERE id= $idPost AND usuario_id = $idUsuarioLogado";
    }
    


    mysqli_query($conexao, $sql) or die(mysqli_error($conexao));       
} // fim atualizarPost



/* Usada em post-exclui.php */
function excluirPost(mysqli $conexao, int $idPost, int $idUsuarioLogado, string $tipoUsuarioLogado){    
    if ($tipoUsuarioLogado == 'admin') {
        $sql = "DELETE FROM posts WHERE id = $idPost" ;
    } else {
        $sql = "DELETE FROM posts WHERE id= $idPost AND usuario_id = $idUsuarioLogado";
    };

	mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
} // fim excluirPost



/* Funções utilitárias */

/* Usada em post-insere.php e post-atualiza.php */
function upload( array $arquivo){
    //defininto o tipo de arquivos aceitos
    $tipoValidos = ["image/png","image/jpeg","image/gif","image/svg+xml"];

    // verificar se o arquivo enviado não e um dos aceitos
    if(!in_array($arquivo['type'], $tipoValidos)){
        die("<script>alert('Formato de arquivo invalido'); history.back();</script>");
    };

    $nome = $arquivo['name']; //$_FILES['arquivo']['name'];

    //Acessando dados de acesso temporario ao arquivo
    $temporario = $arquivo['tmp_name'];

    //Pasta de destino do arquivo que esta sendo enviado
    $destino = "../imagens/$nome";

    /* se o processo de envio temporario para destino for feito com sucesso, então a função retorna verdadeiro (indicando o sucesso no processo) */
    if ( move_uploaded_file ($temporario, $destino)) {
     return true;   
    }
} // fim upload



/* Usada em posts.php e páginas da área pública */
function formataData(string $data):string { 
    /* pegamos a data informada, transformando em texto (strtotime) e depois aplicamos o formato brasileiro dia/mes/ano */
    return date("d/m/Y H:i", strtotime($data));
} // fim formataData



/*** Funções para a área PÚBLICA do site ***/

/* Usada em index.php */
function lerTodosOsPosts(mysqli $conexao):array {
    $sql = "SELECT id, titulo, imagem, resumo FROM posts ORDER BY data DESC ";
    
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $posts = [];
    while( $post = mysqli_fetch_assoc($resultado) ){
        array_push($posts, $post);
    }
    return $posts; 
} // fim lerTodosOsPosts



/* Usada em post-detalhe.php */
function lerDetalhes(mysqli $conexao, int $idPost):array {

    $sql = "SELECT posts.id, posts.titulo, posts.imagem, 
    posts.data, posts.texto, usuarios.nome AS autor FROM posts
    INNER JOIN usuarios ON posts.usuario_id = usuarios.id
    WHERE posts.id = $idPost";

    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    return mysqli_fetch_assoc($resultado); 
} // fim lerDetalhes



/* Usada em search.php */
function busca (mysqli $conexao, string $termo):array {
    $sql = "SELECT id, titulo, 'data', resumo FROM posts WHERE titulo LIKE '%$termo%' OR resumo LIKE '%$termo%' OR texto LIKE '%$termo%' ORDER BY data DESC";
        
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $posts = [];
    while( $post = mysqli_fetch_assoc($resultado) ){
        array_push($posts, $post);
    }
    return $posts; 
} 
// fim busca