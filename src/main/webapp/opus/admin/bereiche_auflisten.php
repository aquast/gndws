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
 * @version     $Id: bereiche_auflisten.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_bereiche_auflisten");
$ueberschrift = $opus->value("ueberschrift_bereiche_auflisten");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
$jahr = $_REQUEST['jahr'];
if (!_is_valid($jahr, 4, 4, "[0-9]+")) {
    die("Fehler in Parameter jahr");
}
$bereich_id = $_REQUEST['bereich_id'];
if (!_is_valid($bereich_id, 0, 20, "[0-9]*")) {
    die("Fehler in Parameter bereich_id");
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
print ("\n<H3>Bitte neue Zugriffsbeschr&auml;nkung ausw&auml;hlen:</H3>\n");
print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/bereich_aendern.$php\"> \n");
print ("Bisherige Zugriffsbeschr&auml;nkung: ");
$res = $opus->query("SELECT bereich, volltext_pfad FROM bereich_$la where bereich_id = $bereich_id");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    print ("<b>$mrow[0]</b>\n<p>\n");
    $volltext_pfad_alt = $mrow[1];
    $opus->free_result($res);
}
print ("Neue Zugriffsbeschr&auml;nkung:\n");
$res = $opus->query("SELECT * FROM bereich_$la");
$num = $opus->num_rows($res);
if ($num > 0) {
    print ("<SELECT NAME=\"bereich_id\" SIZE=1> \n");
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
print ("<P>\n<input type=\"hidden\" name=\"source_opus\" value=\"$source_opus\">");
print ("<P>\n<input type=\"hidden\" name=\"volltext_pfad_alt\" value=\"$volltext_pfad_alt\">");
print ("<P>\n<input type=\"hidden\" name=\"jahr\" value=\"$jahr\">");
print ("\n<input type=\"hidden\" name=\"la\" value=\"$la\">");
print ("\n<input type=\"submit\" value=\"Zugriffsbeschr&auml;nkung &auml;ndern\"> \n");
print ("</FORM> \n");
$opus->close($sock);
$design->foot("bereiche_auflisten.$php", $la);
?>

