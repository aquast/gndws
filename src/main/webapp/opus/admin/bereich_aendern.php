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
 * @version     $Id: bereich_aendern.php 214 2007-05-17 10:28:15Z freudenberg $
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
$db = $opus->value("db");
$table = $opus->value("opus_table");
$url = $opus->value("url");
$titel = $opus->value("titel_bereich_aaendern");
$ueberschrift = $opus->value("ueberschrift_bereiche_aendern");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
$volltext_pfad_alt = $_REQUEST['volltext_pfad_alt'];
$volltext_pfad_alt = _zeichen_loeschen("[`\n\r]", "", $volltext_pfad_alt);
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
$stat = $opus->query("UPDATE $table SET bereich_id = $bereich_id WHERE source_opus = $source_opus");
if ($stat < 0) {
    die("ERROR $table: $ERRMSG <p><HR>\nDatensatz wurde nicht ge&auml;ndert!");
}
$res = $opus->query("SELECT bereich, volltext_pfad FROM bereich_$la where bereich_id = $bereich_id");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $bereich = $mrow[0];
    $volltext_pfad_neu = $mrow[1];
    $opus->free_result($res);
}
if (!file_exists("$volltext_pfad_neu/$jahr")) {
    mkdir("$volltext_pfad_neu/$jahr");
    chmod("$volltext_pfad_neu/$jahr", 0755);
}
if (file_exists("$volltext_pfad_neu/$jahr/$source_opus")) {
    print ("Fehler: $ERRMSG $volltext_pfad_neu/$jahr/$source_opus existiert schon!");
    die;
} else {
    $ret = rename("$volltext_pfad_alt/$jahr/$source_opus", "$volltext_pfad_neu/$jahr/$source_opus");
}
if (!$ret) {
    die("Das Verzeichnis $volltext_pfad_alt/$jahr/$source_opus konnte nicht nach $volltext_pfad_neu/$jahr/$source_opus verschoben werden!");
}
print ("\nDie Zugriffsbeschr&auml;nkung zu IDN $source_opus wurde ge&auml;ndert auf: <b>$bereich</b>.\n<p>");
print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/indexfile.php\"> \n");
print ("<INPUT TYPE=\"submit\"  VALUE=\"Indexdatei neu erstellen\"> \n");
print ("<INPUT TYPE=\"hidden\" NAME=\"von\" VALUE=\"$source_opus\"> \n");
print ("<INPUT TYPE=\"hidden\" NAME=\"bis\" VALUE=\"$source_opus\"> \n");
print ("</FORM> \n");
print ("<P> \n");
print ("<a href=\"aendern.php?suchfeld=source_opus&suchwert=$source_opus&table=$table\">Zur&uuml;ck zum aktualisierten Datensatz</a><p>");
print ("<a href=\"index.php\">Zur&uuml;ck zur Adminseite</a><br><br>\n");
$opus->close($sock);
$design->foot("bereiche_auflisten.$php", $la);
?>

