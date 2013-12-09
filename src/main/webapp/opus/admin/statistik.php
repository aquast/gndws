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
 * @version     $Id: statistik.php 214 2007-05-17 10:28:15Z freudenberg $
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
$opus_table = $opus->value("opus_table");
$url = $opus->value("url");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_statistik");
$ueberschrift = $opus->value("ueberschrift_statistik");
include ("../../lib/stringValidation.php");
$jahr = $_REQUEST['jahr'];
if (!_is_valid($jahr, 4, 4, "[0-9]+")) {
    die("Fehler in Parameter jahr");
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
print ("<H2>Statistik &uuml;ber die in $projekt ver&ouml;ffentlichten Dokumente</H2>");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
$untere_grenze_unix = mktime(0, 0, 0, 1, 1, $jahr);
$obere_grenze_unix = mktime(0, 0, 0, 12, 31, $jahr);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation <= $obere_grenze_unix");
$num_bestand = $opus->num_rows($res);
$opus->free_result($res);
print ("<P>Bestand zum 31.12.$jahr insgesamt: $num_bestand");
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $untere_grenze_unix and date_creation <= $obere_grenze_unix");
$num = $opus->num_rows($res);
$opus->free_result($res);
print ("<P><HR><P>Anzahl der ver&ouml;ffentlichten Dokumente im Jahr $jahr insgesamt: $num");
$jan = mktime(0, 0, 0, 1, 1, $jahr);
$feb = mktime(0, 0, 0, 2, 1, $jahr);
$mar = mktime(0, 0, 0, 3, 1, $jahr);
$apr = mktime(0, 0, 0, 4, 1, $jahr);
$mai = mktime(0, 0, 0, 5, 1, $jahr);
$jun = mktime(0, 0, 0, 6, 1, $jahr);
$jul = mktime(0, 0, 0, 7, 1, $jahr);
$aug = mktime(0, 0, 0, 8, 1, $jahr);
$sep = mktime(0, 0, 0, 9, 1, $jahr);
$okt = mktime(0, 0, 0, 10, 1, $jahr);
$nov = mktime(0, 0, 0, 11, 1, $jahr);
$dez = mktime(0, 0, 0, 12, 1, $jahr);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $jan and date_creation < $feb");
$num_jan = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $feb and date_creation < $mar");
$num_feb = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $mar and date_creation < $apr");
$num_mar = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $apr and date_creation < $mai");
$num_apr = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $mai and date_creation < $jun");
$num_mai = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $jun and date_creation < $jul");
$num_jun = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $jul and date_creation < $aug");
$num_jul = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $aug and date_creation < $sep");
$num_aug = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $sep and date_creation < $okt");
$num_sep = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $okt and date_creation < $nov");
$num_okt = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $nov and date_creation < $dez");
$num_nov = $opus->num_rows($res);
$opus->free_result($res);
$res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $dez and date_creation < $obere_grenze_unix");
$num_dez = $opus->num_rows($res);
$opus->free_result($res);
print ("
<P>
<HR>
<P>Anzahl der ver&ouml;ffentlichten Dokumente im Jahr $jahr pro Monat:
<P>
<table border=1>
<tr>
<th>Januar</th><th>Februar</th><th>M&auml;rz</th><th>April</th><th>Mai</th><th>Juni</th><th>Juli</th><th>August</th><th>September</th><th>Oktober</th><th>November</th><th>Dezember</th>
</tr>
<tr>
<td>$num_jan</td><td>$num_feb</td><td>$num_mar</td><td>$num_apr</td><td>$num_mai</td><td>$num_jun</td><td>$num_jul</td><td>$num_aug</td><td>$num_sep</td><td>$num_okt</td><td>$num_nov</td><td>$num_dez</td>
</tr>

</table>
");
$dok = $opus->query("SELECT * FROM resource_type_$la ORDER BY dokumentart");
$num_dokart = $opus->num_rows($dok);
print ("
<P>
<HR>
<P>Anzahl der ver&ouml;ffentlichten Dokumente im Jahr $jahr nach Dokumentart:
<P>
<table border=1>
<tr>
<th>Dokumentart</th>
<th>Anzahl</th>
</tr>
");
$i = 0;
while ($i < $num_dokart) {
    $i++;
    $mrow = $opus->fetch_row($dok);
    $dokart = $mrow[1];
    $typeid = $mrow[2];
    $res = $opus->query("SELECT source_opus FROM $opus_table WHERE date_creation >= $untere_grenze_unix and date_creation < $obere_grenze_unix and type = $typeid");
    $num = $opus->num_rows($res);
    $opus->free_result($res);
    if ($num > 0) {
        print ("<tr>\n<td>$dokart</td>\n<td>$num</td>\n</tr>\n");
    }
}
print ("</table>");
$opus->free_result($dok);
$fak = $opus->query("SELECT * FROM faculty_$la ORDER BY fakultaet");
$num_fak = $opus->num_rows($fak);
print ("
<P>
<HR>
<P>Anzahl der ver&ouml;ffentlichten Dokumente im Jahr $jahr nach Fakult&auml;ten:
<P>
<table border=1>
<tr>
<th>Fakult&auml;t</th>
<th>Anzahl</th>
</tr>
");
$i = 0;
while ($i < $num_fak) {
    $i++;
    $mrow = $opus->fetch_row($fak);
    $nr = $mrow[0];
    $fakultaet = $mrow[1];
    $inst = $opus->query("SELECT nr from institute_$la WHERE fakultaet = '$nr'");
    $num_inst = $opus->num_rows($inst);
    $suche = "";
    if ($num_inst > 0) {
        $mrow = $opus->fetch_row($inst);
        $suche = "(oi.inst_nr = '$mrow[0]'";
        if ($num_inst > 1) {
            $j = 1;
            while ($j < $num_inst) {
                $j++;
                $mrow = $opus->fetch_row($inst);
                $suche = $suche . " or oi.inst_nr = '$mrow[0]'";
            }
        }
        $suche = $suche . ") ";
    }
    $opus->free_result($inst);
    if ($suche) {
        $suche.= "and oi.source_opus = o.source_opus";
    } else {
        $suche = "oi.source_opus = o.source_opus";
    }
    $select = "SELECT o.source_opus FROM $opus_table o, ";
    $select = $select . $opus_table . "_inst oi";
    $res = $opus->query("$select WHERE date_creation >= $untere_grenze_unix and date_creation < $obere_grenze_unix and $suche");
    $num = $opus->num_rows($res);
    $opus->free_result($res);
    if ($num > 0) {
        print ("<tr>\n<td>$fakultaet</td>\n<td>$num</td>\n</tr>\n");
    }
}
print ("</table>");
$opus->free_result($fak);
$design->foot("statistik.$php", $la);
?>

