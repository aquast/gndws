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
 * @version     $Id: klassifikationen_auflisten.php 214 2007-05-17 10:28:15Z freudenberg $
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
$table = $opus->value("temp_table");
$url = $opus->value("url");
$titel = $opus->value("titel_klassifikationen_auflisten");
$ueberschrift = $opus->value("ueberschrift_klassifikationen_auflisten");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
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
print ("</FONT>\n");
print ("\n<H3>Bitte Klassifikation ausw&auml;hlen:</H3>\n");
print ("Klassifikation:         ");
print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/klassifikationsfeld_anlegen.$php\"> \n");
$res = $opus->query("SELECT * FROM klassifikation_$la");
$num = $opus->num_rows($res);
if ($num > 0) {
    print ("<SELECT NAME=\"subject_type\" SIZE=1> \n");
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        print ("<OPTION VALUE=\"$mrow[0]\"");
        print ("> $mrow[1] \n");
    }
    print ("</SELECT> \n");
    $opus->free_result($res);
}
print ("<P>\n<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\">");
print ("\n<input type=\"hidden\" name=\"la\" value=\"$la\">");
print ("\n<INPUT TYPE = submit  VALUE=\"Klassifikationsfeld anlegen\"> \n");
print ("</FORM> \n");
$opus->close($sock);
$design->foot("klassifikationen_auflisten.$php", $la);
?>

