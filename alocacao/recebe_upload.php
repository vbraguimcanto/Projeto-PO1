<?php
  $_UP['pasta'] = 'upload/';
  $_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
  $_UP['extensoes'] = array('txt');
  $_UP['renomeia'] = true;
  $_UP['erros'][0] = 'Não houve erro';
  $_UP['erros'][1] = 'OOOps! =( O arquivo no upload é maior do que o limite do PHP.';
  $_UP['erros'][2] = 'OOOps! =( O arquivo ultrapassa o limite de tamanho especificado no HTML.';
  $_UP['erros'][3] = 'OOOps! =( O upload do arquivo foi feito parcialmente.';
  $_UP['erros'][4] = 'OOOps! =( Não foi feito o upload de um arquivo.';
  if ($_FILES['arquivo']['error'] != 0) {
    die(" " . $_UP['erros'][$_FILES['arquivo']['error']]);
    exit;
  }
  $extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
  if (array_search($extensao, $_UP['extensoes']) === false) {
    echo "Por favor, envie arquivos com a extensão txt!";
    exit;
  }
  if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
    echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
    exit;
  }
  if ($_UP['renomeia'] == true) {
    $nome_final = md5(time()).'.txt';
  }else {
    $nome_final = $_FILES['arquivo']['name'];
  }
  if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
    //echo "Upload efetuado com sucesso!";
    //echo '<a href="' . $_UP['pasta'] . $nome_final . '"> Clique aqui para acessar o arquivo</a>';
    session_start('criaArquivo');
    ob_start();
    $_SESSION['nomeArquivoSimplex'] = $nome_final;
    $redirect = "algoritmoAlocacao.php";
    header("location:$redirect");
  } else {
    echo "Não foi possível enviar o arquivo, tente novamente!";
  }
?>