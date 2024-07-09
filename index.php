<?php include_once 'config.php' ?>
<!DOCTYPE html>
<html lang="pt-br">
<st>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerar PDF com imagem</title>
  <link rel="stylesheet" href="style.css">
  <style>
    html {
      width: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  </style>
</st>
<body>
  <h1>Gerar PDF com PHP</h1>

  <?php
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(isset($dados['dataInicio']) and isset($dados['dataFinal'])){
      echo "<a href='gerarPdf.php?dataInicio=$dados[dataInicio]&dataFinal=$dados[dataFinal]'>Gerar PDF da Pesquisa</a>";
    } else {
      echo "<a href='gerarPdf.php'>Gerar PDF de todos os registros</a>";
    }
  ?>
  <br><br>

  


  <form action="" method="post">
    <?php
      $dataInicio = "";
      if(isset($dados['dataInicio'])){
        $dataInicio = $dados['dataInicio'];
      }
      $dataFinal = "";
      if(isset($dados['dataFinal'])){
        $dataFinal = $dados['dataFinal'];
      }
    ?>
    <br><br>
    <label>Data de Início</label>
    <input type="date" name="dataInicio" id="dataInicio" value="<?= $dataInicio ?>">    
    <br><br>
    <label>Data Final</label>
    <input type="date" name="dataFinal" id="dataFinal" value="<?= $dataFinal ?>">    
    <br><br>
    <input type="submit" value="Pesquisar" name="pesqUsuario">
  </form>
  
  <?php
    if(!empty($dados['pesqUsuario'])){
      $sql = "SELECT id, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO FROM funcionarios WHERE ADMISSAO BETWEEN :dataInicio AND :dataFinal";
      $query = $pdo->prepare($sql);
      $query->bindParam(':dataInicio', $dados['dataInicio']);
      $query->bindParam(':dataFinal', $dados['dataFinal']);
      $query->execute();
      if($query and $query->rowCount() > 0){
        echo "<div style='margin-top: 10px;'>
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
        while($linha = $query->fetch(PDO::FETCH_ASSOC)){
          extract($linha);
          $dataAdmissao = date('d/m/Y', strtotime($ADMISSAO));
          echo "<tr>
                    <td>$id</td>
                    <td>$NOME</td>
                    <td>$EMAIL</td>
                    <td>$SEXO</td>
                    <td>$DEPARTAMENTO</td>
                    <td>$dataAdmissao</td>
                  </tr>";
        }
      } else {
        echo "<p>Erro: Nenhum resultado encontrado!</p>";
      }
      echo "</tbody>
          </table>
        </div>";
    }
  ?>

</body>
</html>