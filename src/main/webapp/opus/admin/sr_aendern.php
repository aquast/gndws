<?php
/**
 * Short description for file
 * 
 * This file is part of OPUS. The software OPUS has been developed at the
 * University of Stuttgart with funding from the German Research Net
 * (Deutsches Forschungsnetz), the Federal Department of Higher Education and
 * Research (Bundesministerium fuer Bildung und Forschung) and The Ministry of
 * Science, Research and the Arts of the State of Baden-Wuerttemberg
 * (Ministerium fuer Wissenschaft, Forschung und Kunst des Landes
 * Baden-Wuerttemberg).
 * 
 * PHP versions 4 and 5
 * 
 * OPUS is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * OPUS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * @package     OPUS
 * @author      Emily Erdbeer <emily@example.org>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id: sr_aendern.php 214 2007-05-17 10:28:15Z freudenberg $
 */
#############################################################
# letzte Änderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
include ("../../lib/stringValidation.php");
$sr_id = $_REQUEST['sr_id'];
if (!_is_valid($sr_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter sr_id");
}
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$titel = $opus->value("titel_sr_aendern");
$ueberschrift = $opus->value("ueberschrift_sr_aendern") . " " . $sr_id;
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
print ("<FONT COLOR=red>");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
$res = $opus->query("SELECT name FROM schriftenreihen WHERE sr_id=$sr_id");
$num = $opus->num_rows($res);
if ($num > 0) {
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        $sr_name = htmlspecialchars($mrow[0]);
        print ("Schriftenreihe ID: $sr_id; Bisheriger Name: \"$sr_name\"<p>");
        print ("Neuer Name: \n");
?>
	<form method="post" action="sr_replace.php" name="ReplaceForm">
	<input type="text" name="new_name" size="60" maxlength="255" value="<? echo $sr_name; ?>">
	<input type="hidden" name="sr_id" value="<? echo $sr_id; ?>">
	<input type="hidden" name="la" value="<? echo $la; ?>">
	
	<input type="submit" value="&Auml;ndern">
	</form>

	<script type="text/javascript">
	<!--
	document.ReplaceForm.new_name.focus();
	//-->
	</script>
	
	<UL>
	<LI><A HREF="schriftenreihen.php?la=<? echo $la; ?>">Zur&uuml;ck</A></LI>
	</UL>
<?php
    }
    $opus->free_result($res);
    $opus->close($sock);
}
$design->foot("sr_aendern.$php", $la);
?>
