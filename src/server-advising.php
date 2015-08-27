<?php

function formatThesis($diss) {
  $s = "";

      $s .= "<ul><li><strong>" . $diss['Title'] . "</strong>";

      if (isset($diss['Advisor']) && strlen(trim($diss['Advisor'])) != 0) { $s .= " (as co-advisor)"; }

      $s .= ", "; 
      for ($ax = 0; $ax < count($diss['Authors']); $ax++) {
        $s .= $diss['Authors'][$ax];
        if ($ax != count($diss['Authors'])-1) $s .= ', ';
      }
      $s .= ", " . $diss['tptype'] . ". ";
      if ($diss['University']) { $s .= $diss['University']; }
      $s .= " (";
      if ($diss['Starting_Year'] && $diss['Starting_Year'] != 0) { $s .= /*int_to_month($diss['Starting_Month']) . " " . */ $diss['Starting_Year']; }
      $s .= "-";
      if ($diss['Year'] && $diss['Year'] != 0) { $s .= /*int_to_month($diss['Month']) . " " . */ $diss['Year']; }
      $s .= "). ";
      if (!isset($diss['Advisor']) || strlen(trim($diss['Advisor'])) == 0) {
        if (isset($diss['CoAdvisor']) && strlen(trim($diss['CoAdvisor'])) != 0) { $s .= $diss['CoAdvisor'] . ", co-advisor."; }
        //if (isset($diss['CoAdvisorEntidade']) && strlen(trim($diss['CoAdvisorEntidade'])) != 0) { $s .= " -- " . $diss['CoAdvisorEntidade']; }
        //$s .= ".";
      }
      else {
        if (isset($diss['Advisor']) && strlen(trim($diss['Advisor'])) != 0) { $s .= $diss['Advisor'] . ", advisor."; }
        //if (isset($diss['AdvisorEntidade']) && strlen(trim($diss['Advisor'])) != 0) { $s .= " -- " . $diss['AdvisorEntidade']; }
        //$s .= ".";
        // person is co-advisor
      }
      $s .= "</li></ul>";

  return $s;
}

function advisingById($idDBLink, $id) {
  $id = $id[0]; //FRANCISCO: FIXME
  $errno = 1;

  // will present: ongoing (i.e. not published); by year of publication; for each publication year, order by start year and title

  // Dissertacao:
  //    PubId,Starting_Year,Starting_Month,University,AdvisorId,CoAdvisorId,
  //    Location,AdvisorEntidadeId,CoAdvisorEntidadeId
  //      --> AdvisorId=$id
  //
  $query = "SELECT *, TipoPublicacao.Type as tptype FROM Dissertacao
            LEFT JOIN Autores ON Dissertacao.pubid=Autores.pubid
            LEFT JOIN Pubs ON Dissertacao.pubid=Pubs.pubid
            LEFT JOIN Pessoal ON Autores.authorId=Pessoal.pessoaId
            LEFT JOIN TipoPublicacao ON Pubs.Type = TipoPublicacao.typeId
            WHERE (Dissertacao.AdvisorId=". trim($id) . " OR Dissertacao.CoAdvisorId=". trim($id) . ")
            ORDER BY Year desc, Month desc, Starting_Year desc, Starting_Month desc, title asc";
  $result = odbc_exec($idDBLink, $query);

  $ads = Array();
  while(odbc_fetch_row($result)) {
    $year = odbc_result($result, 'Year');
    if ($year == "") $year = 'Ongoing';
    $title = odbc_result($result, 'Title');
    $ads[$year][$title]['Month'] = odbc_result($result, 'Month');
    $ads[$year][$title]['Year']  = $year;
    $ads[$year][$title]['Title']  = $title;
    $ads[$year][$title]['tptype']  = odbc_result($result, 'tptype');
    $ads[$year][$title]['Starting_Year']  = odbc_result($result, 'Starting_Year');
    $ads[$year][$title]['Starting_Month'] = odbc_result($result, 'Starting_Month');
    $ads[$year][$title]['University'] = odbc_result($result, 'University');
    $ads[$year][$title]['Authors'][] = odbc_result($result, 'abrvNome');
    // sometimes the advisor and co-advisor are the same in the database...
    if ($id != odbc_result($result, 'CoAdvisorId')) {
      $ads[$year][$title]['CoAdvisor'] = nameById($idDBLink, odbc_result($result, 'CoAdvisorId'));
      $ads[$year][$title]['CoAdvisorEntidade'] = instituteById($idDBLink, odbc_result($result, 'CoAdvisorEntidadeId'));
    }
    if ($id != odbc_result($result, 'AdvisorId')) {
      $ads[$year][$title]['Advisor'] = nameById($idDBLink, odbc_result($result, 'AdvisorId'));
      $ads[$year][$title]['AdvisorEntidade'] = instituteById($idDBLink, odbc_result($result, 'AdvisorEntidadeId'));
    }
  }

  $s = "";
  if ($ads['Ongoing']) {
    $s .= "<h3>Ongoing</h3>";
    foreach ($ads['Ongoing'] as $title => $diss) { $s .= formatThesis($diss); }
  }
  foreach ($ads as $year => $ydiss) {
    if ($year == 'Ongoing') continue;
    $s .= "<h3>Finished in $year</h3>";
    foreach ($ydiss as $title => $diss) { $s .= formatThesis($diss); }
      //$s .= "<ul><li><strong>" . $title . "</strong> (" . $diss['tptype'] . ")<br/>";
      //if (count($diss['Authors']) > 1) $s .= "<b>Authors: </b> ";
      //else $s .= "<b>Author: </b> ";
      //for ($ax = 0; $ax < count($diss['Authors']); $ax++) {
        //$s .= $diss['Authors'][$ax];
        //if ($ax != count($diss['Authors'])-1) $s .= ', ';
      //}
      //if ($diss['University']) { $s .= "<br/><b>Institution: </b> " . $diss['University']; }
      //$s .= "<br/>";
      //if ($diss['Starting_Year'] != '') { $s .= "<b>Date started: </b> " . int_to_month($diss['Starting_Month']) . " " . $diss['Starting_Year'] . " -- "; }
      //if ($year != 'Ongoing') { $s .= "<b>Date finished: </b> " . int_to_month($diss['Month']) . " " . $year; }
      //if ($diss['CoAdvisor']) { $s .= "<br/><b>Co-advisor: </b>" . $diss['CoAdvisor']; }
      //if ($diss['CoAdvisorEntidade']) { $s .= " -- " . $diss['CoAdvisorEntidade']; }
      //$s .= "<br/></li></ul>";
    //}
  }

  if ($s) $s = "<h2>Activity as Advisor</h2>" . $s;
  return $s;
}

?>
