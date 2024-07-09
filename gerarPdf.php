<?php

ob_start();

  include 'config.php';
  require_once 'vendor/autoload.php';

  //Receber da URL o termo usado para pesquisar
  $dataInicio = filter_input(INPUT_GET, 'dataInicio', FILTER_DEFAULT);
  $dataFinal = filter_input(INPUT_GET, 'dataFinal', FILTER_DEFAULT);


  if(!empty($dataInicio) and !empty($dataFinal)){
    $sql = "SELECT id, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO
    FROM funcionarios
    WHERE ADMISSAO BETWEEN :dataInicio AND :dataFinal
    ORDER BY id ASC";
    $query = $pdo->prepare($sql);
    $query->bindParam(':dataInicio', $dataInicio, PDO::PARAM_STR);
    $query->bindParam(':dataFinal', $dataFinal, PDO::PARAM_STR);
  } else {
    $sql = "SELECT id, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO
    FROM funcionarios
    WHERE ADMISSAO ORDER BY id ASC";
    $query = $pdo->prepare($sql);
  }
  
  $query->execute();

  use Dompdf\Dompdf;
  use Dompdf\Options;
  //Definindo fonte no ato de inicialização
  $options = new Options();
  $options->set('defaultFont', 'Helvetica');
  $options->set('isRemoteEnabled', true);

  //Inicializando a aplicação de PDF com a configuração de fonte
  $dompdf = new Dompdf($options);
  
  $html = "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Gerar PDF</title>
              <link rel='stylesheet' href='http://localhost:81/relatorioPdf/css/estilo.css'></link>
            </head>
            <body>
              <img src='http://localhost:81/relatorioPdf/img/ferrari.png' class='imagem'>
              <div class='container-tabela'>
                <table border='1'>
                  <thead>
                    <tr>
                      <td>ID</td>
                      <td>NOME</td>
                      <td>EMAIL</td>
                      <td>SEXO</td>
                      <td>DEPARTAMENTO</td>
                      <td>ADMISSAO</td>
                    </tr>
                  </thead>
                  <tbody>";

  if($query){
    if($linha = $query->rowCount() > 0){
      while($linha = $query->fetch(PDO::FETCH_ASSOC)){
        $admissao = DateTime::createFromFormat('Y-m-d',$linha['ADMISSAO'])->format('d/m/Y');
        $html .= "<tr>
                    <td>$linha[id]</td>
                    <td>$linha[NOME]</td>
                    <td>$linha[EMAIL]</td>
                    <td>$linha[SEXO]</td>
                    <td>$linha[DEPARTAMENTO]</td>
                    <td>$admissao</td>
                  </tr>";
      }
    } else {
      $html .= "<tr>
                  <td colspan='5'>Nenhum dado encontrado</td>
                </tr>";
    }
    $html .= "</tbody>
            </table>
          </div>
        </body>
      </html>";
  }

  //Inserindo o conteúdo a ser impresso
  $dompdf ->loadHtml($html);
  
  //Definindo papel e orientação
  $dompdf->setPaper('A4', 'portrait');

  //Renderiza o HTML em PDF
  $dompdf->render();

  //Gera o arquivo PDF
  $dompdf->stream();


