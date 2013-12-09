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
 * @version     $Id: klassifikationsfeld_anlegen.php 322 2007-09-13 14:53:15Z marahrens $
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
$url = $opus->value("url");
$db = $opus->value("db");
$table = $opus->value("temp_table");
$titel = $opus->value("titel_klassifikationsfeld_anlegen");
$ueberschrift = $opus->value("ueberschrift_klassifikationsfeld_anlegen");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter source_opus");
}
$subject_type = $_REQUEST['subject_type'];
$subject_type = _zeichen_loeschen("[`\n\r]", "", $subject_type);
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$sock = $opus->connect();
if ($sock < 0 || $opus->select_db() < 0) {
    print ("<FONT COLOR=red>");
    echo ("Error: $ERRMSG\n</TABLE></TABLE>");
    print ("</FONT>");
}
$res = $opus->query("SELECT subject_type FROM $table WHERE source_opus= $source_opus ");
$num = $opus->num_rows($res);
$mrow = $opus->fetch_row($res);
if ($num > 0) {
    if ($mrow[0] != "") {
        require ("../../lib/design.$php");
        $design = new design;
        $design->head_titel($titel);
        $design->head_ueberschrift($ueberschrift, $la);
        print ("Es besteht bereits ein Klassfikationsfeld f&uuml;r IDN $source_opus. Pro Datensatz kann nur ein Klassifikatonsfeld angelegt werden.<br>\n");
        print ("<a href=\"aendern.php?suchfeld=source_opus&suchwert=$source_opus&table=$table&la=$la\">Zur&uuml;ck zum aktualisierten Datensatz</a><br>");
        $design->foot("klassifikationsfeld_anlegen.$php", $la);
    } else {
        $stat = $opus->query("UPDATE $table SET subject_type = '$subject_type' WHERE source_opus= $source_opus ");
        header("Location: $url/admin/aendern.php?table=$table&suchfeld=source_opus&suchwert=$source_opus&la=$la");
    }
} else {
    require ("../../lib/design.$php");
    $design = new design;
    $design->head_titel($titel);
    $design->head_ueberschrift($ueberschrift, $la);
    print ("Datensatz $source_opus nicht vorhanden.<br>\n");
    print ("<a href=\"index.php?la=$la\">Zur&uuml;ck zur Adminseite</a><br><br>\n");
    $design->foot("klassifikationsfeld_anlegen.$php", $la);
}
$opus->free_result($res);
$opus->close($sock);
?>

