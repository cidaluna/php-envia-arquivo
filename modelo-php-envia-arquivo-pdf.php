<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// recebe as Variaveis
$nome     = $_POST["camponome"];
$email    = $_POST["campoemail"];
$celular   = $_POST["campocelular"];
$escolaridade  = $_POST["campoescolaridade"];
$areadeinteresse  = $_POST["campoarea"];
$observacoes  = $_POST["campoobservacoes"];
$assunto     = $_POST["campoassunto"]; 
$limite     = 1048576; // 1Mb 
$conteudocompleto = "<p>Novo Curriculo enviado em: " .date('d/m/Y').
"<p><strong>Nome: </strong>" .$nome.
"</p><p><strong>Email: </strong>" .$email.
"</p><p><strong>Celular: </strong>" .$celular.
"</p><p><strong>Escolaridade: </strong>" .$escolaridade.
"</p><p><strong>Area de Interesse: </strong>" .$areadeinteresse.
"</p><p><strong>Obs.(campo opcional): </strong>" .$observacoes.
"</p><p><strong>Curriculo: </strong> Verificar em Anexo.</p>";
// Para quem vai ser enviado o email
$email_from = "contato@seusite.com.br"; //se o email from nao for valido corre o risco de nao chegar nada na caixa de mensagens 
$nome_from = "Curriculo Site";
$para01 = "contato@seusite.com.br";
if (!ereg("^([0-9,a-z,A-Z]+)([.,_]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$", $email)){
echo "<script>alert('Digite um email valido para possivel contato!');</script>";
echo "<script>window.location='curriculo.html';</script>";  //se o email nao for valido, retorna pra pagina curriculo e sai desse php
exit;
}
$arquivo     = $_FILES["arquivo"];
// Faz a verificação do tamanho do arquivo
$tamanhodoarquivo = $_FILES['arquivo']['size'];
if ($limite < $tamanhodoarquivo){
echo "<script>alert('O arquivo enviado ultrapassou o limite de 1MB.');</script>";
    // obtendo a página anterior
    $url_ant = $_SERVER['HTTP_REFERER'];
    // redirecionando imediatamente
    echo "<meta http-equiv=\"refresh\" content=\"0;url=$url_ant\" />";
  exit;
}

// Array com as extensões permitidas
$_UP['extensoes'] = array('pdf');
// Faz a verificação da extensão do arquivo
$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
if (array_search($extensao, $_UP['extensoes']) === false) {
echo "<script>alert('Por favor, envie arquivo com a extensao pdf');</script>"; //só aceita documento em formato pdf
    // obtendo a página anterior
    $url_ant = $_SERVER['HTTP_REFERER'];
    // redirecionando imediatamente
    echo "<meta http-equiv=\"refresh\" content=\"0;url=$url_ant\" />";
  exit;
}


if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
$fp = fopen($_FILES["arquivo"]["tmp_name"],"rb");
$anexo = fread($fp,filesize($_FILES["arquivo"]["tmp_name"]));
$anexo = base64_encode($anexo);
fclose($fp);
$anexo = chunk_split($anexo);
$boundary = "XYZ-" . date("dmYis") . "-ZYX";
$mens = "--$boundary\n";
$mens .= "Content-Transfer-Encoding: 8bits\n";
$mens .= "Content-Type: text/html; charset=\"UTF-8\"\n\n"; //plain
$mens .= "$conteudocompleto\n";
$mens .= "--$boundary\n";
$mens .= "Content-Type: ".$arquivo["type"]."\n";
$mens .= "Content-Disposition: attachment; filename=\"".$arquivo["name"]."\"\n";
$mens .= "Content-Transfer-Encoding: base64\n\n";
$mens .= "$anexo\n";
$mens .= "--$boundary--\r\n";
$headers = "MIME-Version: 1.0\n";
$headers .= "From: \"$nome_from\" <$email_from>\r\n";
$headers .= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";
$headers .= "$boundary\n";
//envio o email com o anexo
mail($para01,$assunto,$mens,$headers);
//mail($para02,$assunto,$mens,$headers); eh possivel inserir mais destinatarios aqui
echo "<script>alert('Email com arquivo enviado com Sucesso!');</script>";
echo "<script>window.location='agradecimento.html';</script>"; //redireciona para a pagina de agradecimento
}
}
else{
echo "<script>alert('Formulario nao enviado, preencha novamente!');</script>";
echo "<script>window.location='curriculo.html';</script>"; //retorna para a pagina curriculo.html
}
?>