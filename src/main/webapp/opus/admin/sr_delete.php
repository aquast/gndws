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
 * @version     $Id: sr_delete.php 214 2007-05-17 10:28:15Z freudenberg $
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
$temp_table = $opus->value("temp_table");
$url = $opus->value("url");
$opus_table_sr = $opus_table . "_schriftenreihe";
$temp_table_sr = $temp_table . "_schriftenreihe";
$titel = $opus->value("titel_sr_loeschen");
$ueberschrift = $opus->value("ueberschrift_sr_loeschen");
include ("../../lib/stringValidation.php");
$sr_id = $_REQUEST['sr_id'];
if (!_is_valid($sr_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter sr_id");
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
print ("</FONT>");
$res = $opus->query("SELECT name FROM schriftenreihen WHERE sr_id=$sr_id");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $sr_name = htmlspecialchars($mrow[0]);
    $opus->free_result($res);
} else {
    print ("<H3> Es existiert keine Schriftenreihe mit der ID $sr_id.</H3>");
    print ("<P><A HREF=\"$url/admin/\">Adminseite</A> \n");
    $design->foot("sr_delete.$php", $la);
    exit();
}
$res = $opus->query("SELECT source_opus, sr_id, sequence_nr FROM $opus_table_sr
                        WHERE sr_id = '$sr_id' ");
$res2 = $opus->query("SELECT source_opus, sr_id, sequence_nr FROM $temp_table_sr
                         WHERE sr_id = '$sr_id' ");
$num = $opus->num_rows($res);
$num2 = $opus->num_rows($res2);
if (($num+$num2) > 0) {
    print ("Es existieren B&auml;nde zur Schriftenreihe <b>$sr_name</b>, daher kann Sie nicht geloescht werden.</H3>");
    print ("<P><A HREF=\"$url/admin/\">Adminseite</A> \n");
} else {
    $stat = $opus->query("DELETE FROM schriftenreihen WHERE sr_id = $sr_id");
    $anzahl = $opus->affected_rows();
    if ($anzahl == 0) {
        echo ("ERROR-STATUS: $stat.Es wurde kein Datensatz in $table gel&ouml;scht. \n");
        print ("<P><A HREF=\"$home/admin/index.php?la=$la\">Adminseite</A> \n");
        $design->foot("sr_delete.$php", $la);
        exit;
    } else {
        print ("<H3>Die Schriftenreihe $sr_name wurde gel&ouml;scht</H3>");
        print ("<P><A HREF=\"$url/admin/\">Adminseite</A> \n");
    }
}
$design->foot("sr_delete.$php", $la);
?>

