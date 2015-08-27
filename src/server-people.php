<?php

require_once 'server-config.php';
require_once 'server-util.php';

$idHab = array(1 => 'Agrega&ccedil;&atilde;o', 2 => 'PhD', 3 => 'MSc', 4 => 'BSc', 5 => 'Undergraduate', 6 => '[6]', 7 => '[7]');
$idCat = array(1 => 'Full Professor', 2 => 'Associate Professor', 3 => 'Assistant Professor', 4 => 'Lecturer', 5 => 'Trainee Lecturer', 6 => 'Teaching Assistant', 7 => 'N/A');
$idLig = array(1 => 'Researcher', 2 => 'Associated Researcher', 3 => 'Contract', 4 => 'FCT Scholarship Holder', 5 => '5', 6 => '6', 7 => 'Scholarship Holder',
               8 => '[8]', 9 => '[9]', 10 => '[10]', 11 => '[11]', 12 => 'ADM', 13 => '[13]');

function personById($idDBLink, $id) {
  // Tabela: PessoalServer
  // Colunas: pessoaId, nome, numero, ligacao, telefone, email, sala,
  //          CC, url, admissao, dataInactivacao, financiamento, entidade,
  //          habilitacao, categoria, ListaFct, plainpassword, password, foto

  global $idHab, $idCat, $idLig;
  $result = odbc_exec($idDBLink, "SELECT pessoaId,nome,numero,ligacao,telefone,email,sala,url,habilitacao,categoria,ListaFct,foto,admissao,dataInactivacao FROM PessoalServer where numero=\"" . trim($id) . "\"");
  /*
  FRANCISCO: FIXME
  if (@mysql_num_rows($result) == 0)
    $result = iidQuery("SELECT pessoaId,nome,numero,ligacao,telefone,email,sala,url,habilitacao,categoria,ListaFct,foto,admissao,dataInactivacao FROM PessoalEstagiario where numero=\"" . trim($id) . "\"");
  }
  */

  $s = "";
  while(odbc_fetch_row($result)) {
    $s .= "<h2>Server Information</h2>";
    $s .= "<ul><li><strong>" . odbc_result($result, 'nome') . "</strong><br/>";
    //$s .= "<strong>Phone extension: </strong>" . odbc_result($result, 'telefone') . "<br/>";
    $s .= "<strong>E-mail: </strong><a href='mailto:" . odbc_result($result, 'email') . "'>" . odbc_result($result, 'email') . "</a><br/>";
    //$s .= "<strong>Room: </strong>" . odbc_result($result, 'sala') . "<br/>";
    $s .= odbc_result($result, 'url')!= '' ? "<strong>URL: </strong><a href='" . odbc_result($result, 'url') . "'>" . odbc_result($result, 'url') . "</a><br/>" : "";
    $s .= "<strong>Connection: </strong>" . $idLig[odbc_result($result, 'ligacao')] . "<br/>";
    $s .= "<strong>Academic Degree: </strong>" . $idHab[odbc_result($result, 'habilitacao')] . "<br/>";
    if ($idCat[odbc_result($result, 'categoria')] != 'N/A')
      $s .= "<strong>Category: </strong>" . $idCat[odbc_result($result, 'categoria')] . "<br/></li>";
    $s .= "</ul>";
  }

  return $s;
}

function personsByCC($idDBLink, $cc) {

// Investigadores

// select distinct * from Pessoal left join PessoalServer on Pessoal.pessoaId = PessoalServer.pessoaId where Pessoal.status='Activo' and PessoalServer.CC='IP02' order by Pessoal.pessoaId;

// select distinct * from Pessoal left join PessoalServer on Pessoal.pessoaId = PessoalServer.pessoaId where Pessoal.status='Externo' and PessoalServer.CC='IP02' order by Pessoal.pessoaId;

// select distinct * from Pessoal left join PessoalServer on Pessoal.pessoaId = PessoalServer.pessoaId where Pessoal.status='Pendente' and PessoalServer.CC='IP02' order by Pessoal.pessoaId;

// select distinct * from Pessoal left join PessoalServer on Pessoal.pessoaId = PessoalServer.pessoaId where Pessoal.status='Inactivo' and PessoalServer.CC='IP02' order by Pessoal.pessoaId;

// Estagiarios
// pessoaId|abrvNome|status|pessoaId|nome|morada|localidade|codigoPostal|telemovelPrivado|telefonePrivado|isAluno|numeroAluno|outros|BI|observacoes|propostoPor|dataProposto|admissao|dataInactivacao|CC|email|url|numero|extensao|plainpassword|password|foto|sala|andar|entidade|ligacao|habilitacao|

// select distinct * from Pessoal left join PessoalEstagiario on Pessoal.pessoaId = PessoalEstagiario.pessoaId where Pessoal.status='Activo' and PessoalEstagiario.CC='IP02' order by Pessoal.pessoaId; 

// Externo

// select distinct * from Pessoal left join PessoalEstagiario on Pessoal.pessoaId = PessoalEstagiario.pessoaId where Pessoal.status='Inactivo' and PessoalEstagiario.CC='IP02' order by Pessoal.pessoaId;

// Pendente

}

?>
