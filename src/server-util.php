<?php

require_once "server-config.php";

function nameById($idDBLink, $id) {
  $result = odbc_exec($idDBLink, "SELECT nome FROM PessoalServer WHERE pessoaId=\"" . trim($id) . "\"");
  if (!$result)
    $result = odbc_exec($idDBLink, "SELECT nome FROM PessoalEstagiario WHERE pessoaId=\"" . trim($id) . "\"");
  $s = "";
  while(odbc_fetch_row($result)) { $s .= odbc_result($result, 'nome'); }
  return $s;
}

// returns field 'instituicao' (a summary of the institution's description)
function instituteById($idDBLink, $id) {
  $rs = odbc_exec($idDBLink, "SELECT * FROM Instituicoes WHERE instituicaoId=\"" . trim($id) . "\"");
  $s = "";
  while(odbc_fetch_row($rs)) { $s .= odbc_result($rs, 'nome'); }
  //if(odbc_fetch_row($rs)) { $s .= odbc_result($rs, 'nome'); }
  return $s;
}

function authorIdByNumber($idDBLink, $ids) {
  if (!$idDBLink) { exit("Connection Failed: authorIdByNumber"); }
  $pids = array();
  foreach ($ids as $id) {
    $result1 = odbc_exec($idDBLink, "SELECT pessoaId FROM PessoalServer WHERE numero=\"$id\"");
    if ($result1) {
      if (odbc_fetch_row($result1))
        $pids[] = odbc_result($result1, 'pessoaId');
    }
    $result2 = odbc_exec($idDBLink, "SELECT pessoaId FROM PessoalEstagiario WHERE numero=\"$id\"");
    if ($result2) {
      if (odbc_fetch_row($result2))
        $pids[] = odbc_result($result2, 'pessoaId');
    }
  }
  return $pids;
}

function int_to_month($month) {
  switch ($month) {
  case 1: $ret="January"; break;
  case 2: $ret="February"; break;
  case 3: $ret="March"; break;
  case 4: $ret="April"; break;
  case 5: $ret="May"; break;
  case 6: $ret="June"; break;
  case 7: $ret="July"; break;
  case 8: $ret="August"; break;
  case 9: $ret="September"; break;
  case 10: $ret="October"; break;
  case 11: $ret="November"; break;
  default: $ret="December"; break;
  }
  return $ret;
}

?>
