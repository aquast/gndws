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
 * @version     $Id: abfrage_loeschen.php 189 2007-04-25 11:57:13Z freudenberg $
 */
#############################################################
# letzte Aenderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$url = $opus->value("url");
$titel = $opus->value("titel_abfrage_loeschen");
$ueberschrift = $opus->value("ueberschrift_abfrage_loeschen");
include ("../../lib/stringValidation.php");
$table = $_REQUEST['table'];
if ($table != $opus_table && $table != $temp_table) {
    die("Fehler in Parameter table");
}
$bereich_id = $_REQUEST['bereich_id'];
if (!_is_valid($bereich_id, 0, 20, "[0-9]*")) {
    die("Fehler in Parameter bereich_id");
}
$jahr = $_REQUEST['jahr'];
if (!_is_valid($jahr, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter jahr");
}
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
$type = $_REQUEST['type'];
if (!_is_valid($type, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter type");
}
$title = $_REQUEST['title'];
$title = _zeichen_loeschen("[`\n\r]", "", $title);
$creator_name = $_REQUEST['creator_name'];
$creator_name = _zeichen_loeschen("[`\n\r]", "", $creator_name);
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
$title = stripslashes($title);
$creator_name = stripslashes($creator_name);
print ("
Soll der Datensatz: <br> 
<h3>Titel: $title <br> 
Autor: $creator_name</h3> 
in der Tabelle <font size=+2><b>$table</b></font> gel&ouml;scht werden?</H3> 

<table>
<tr>
<td>
<FORM METHOD = \"POST\" ACTION = \"$url/admin/loeschen.$php\">
<INPUT TYPE = \"submit\"  VALUE= \"L&ouml;schen\">
<INPUT TYPE = \"hidden\" NAME = \"table\" VALUE = \"$table\">
<INPUT TYPE = \"hidden\" NAME= \"bereich_id\" VALUE=\"$bereich_id\">
<INPUT TYPE = \"hidden\" NAME = \"jahr\" VALUE = \"$jahr\">
<INPUT TYPE = \"hidden\" NAME = \"source_opus\" VALUE = \"$source_opus\">
<INPUT TYPE = \"hidden\" NAME = \"type\" VALUE = \"$type\">
<INPUT TYPE = \"hidden\" NAME= \"la\" value=\"$la\">
</FORM>
</td>

<td>
<FORM METHOD = \"POST\" ACTION = \"$url/admin/\">
<INPUT TYPE = submit  VALUE=\"Nein\">
</FORM>
</td>
</tr>
</table>
");
$design->foot("abfrage_loeschen.$php", $la);
?>

