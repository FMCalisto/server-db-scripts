<?php

require_once 'server-config.php';
require_once 'server-util.php';
require_once 'server-people.php';
require_once 'server-pubs.php';
require_once 'server-advising.php';
require_once 'server-calls.php';


# the function registered by the extension gets the text between the
# tags as input and can transform it into arbitrary HTML code.
# Note: The output is not interpreted as WikiText but directly
#       included in the HTML output. So Wiki markup is not supported.
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/YourExtensionName.php");

$wgExtensionFunctions[] = "wfExampleExtension";

//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//
//  Register the extension with the WikiText parser.
//  The first parameter is the name of the new tag. In this case it defines
//  the tag:
//        <server> ... </server>
//  The second parameter is the callback function for processing the text
//  between the tags.
//
function wfExampleExtension() {
  global $wgParser;  // MediaWiki global variable
  $wgParser->setHook("server", "renderServer");
}

//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//
//  The callback function for converting the input text to HTML output.
//  The function registered by the extension gets the text between the
//  tags as $input and transforms it into arbitrary HTML code.
//  Note: the output is not interpreted as WikiText but directly included in
//  the HTML output. So Wiki markup is not supported.
//
//  To activate the extension, include it from your LocalSettings.php
//  with: include("extensions/YourExtensionName.php");
//
//  $argv is an array containing any arguments passed to the extension like:
//     <server what="foo" bar>..
//
//  According to the metawiki, this works in MediaWiki 1.5.5.
//   <server what="person" id="62">This text is not actually used</server>
//
// Personal information:
//    <server what='person' id='62'></server>
//
// Information for a group:
//    <server what='publications' cc='IP02'></server>
//

/*
 * Please change the next attributes:
 *
 * NAME - Your server NAME;
 * user - Your server user;
 * password - Your server password;
 *
 */

function renderServer($input, $argv) {
  // connect to the database
  $idDBLink = odbc_connect('NAME', 'user', 'password');
  if (!$idDBLink) { exit("Connection to database failed! Please contact your DataBase Administrator."); }

  $html = "";
  if ($argv['what'] == 'person') {
    $id = split(",", trim($argv["id"]));
    if ($id != '') {
      // information about someone:
      //  1. personal contacts and summary
      //  2. publications by person
      //  3. advisory work by person
      //
      $html .= personById($idDBLink, $id[0]);

      $internalIds = authorIdByNumber($idDBLink, $id);  // use all Ids
      $html .= pubsById($idDBLink, $internalIds);
      $html .= advisingById($idDBLink, $internalIds);
    }
    
  }
  else if ($argv['what'] == 'advising') {
    $id = split(",", trim($argv["id"]));
    if ($id != '') {
      $internalIds = authorIdByNumber($idDBLink, $id);  // use all Ids
      $html .= iconv('latin1', 'UTF-8', advisingById($idDBLink, $internalIds));
    }

  }
  else if ($argv['what'] == 'publications') {
    // information about some "centro de custo":
    //  1. currently, only a list of publications
    //
    $cc = trim($argv["cc"]);
    $id = trim($argv["id"]);
    if ($cc != '') {
      $html .= iconv('latin1', 'UTF-8', pubsByCC($idDBLink, $cc));
    }
    else if ($id != '') {
      $html .= iconv('latin1', 'UTF-8', pubsById($idDBLink, authorIdByNumber($idDBLink, array($id))));
    }
  }
  /*else if ($argv['what'] == 'publications') {
    // information about some "centro de custo":
    //  1. currently, only a list of publications
    //
    $cc = trim($argv["cc"]);
    if ($cc != '') {
      $html .= pubsByCC($idDBLink, $cc);
    }
  }*/
  else if ($argv['what'] == 'calls') {
    // information about some "centro de custo":
    //  1. currently, only a list of publications
    //
    $cc = trim($argv["cc"]);
    $showClosed = isset($argv['showclosed']) ? trim($argv['showclosed']) : "";
    if ($cc != '') {
      $html .= iconv('latin1', 'UTF-8', callsByCC($idDBLink, $cc, $showClosed == "yes"));
    }
  }
  else {
    // oops! no text...
  }

  odbc_close($idDBLink);
  return $html;
}

?>
