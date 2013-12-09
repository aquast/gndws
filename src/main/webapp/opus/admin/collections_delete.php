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
 * @version     $Id: collections_delete.php 214 2007-05-17 10:28:15Z freudenberg $
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
$home = $opus->value("home");
$projekt = $opus->value("projekt");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$opus_coll = $opus_table . "_coll";
$temp_coll = $temp_table . "_coll";
$titel = $opus->value("titel_collections_update");
$ueberschrift = $opus->value("ueberschrift_collections_update");
include ("../../lib/stringValidation.php");
$coll_id = $_REQUEST['coll_id'];
if (!_is_valid($coll_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_id");
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
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
//echo "<p>Id der Collection die weg muss $coll_id</p>";
// Wir brauchen verschiedene Werte aus der Datenbank
$query = "SELECT root_id, lft, rgt FROM collections WHERE coll_id = '$coll_id';";
//echo "<p>Query: $query</p>";
$res = $opus->query($query);
$num = $opus->num_rows($res);
if ($num < 1) { // es gibt keinen Datenbankeintrag für die Anfrage..
    echo "<p>Seltsames geht in der Datenbank vor. Der Eintrag coll_id $coll_id wurde nicht gefunden</p>";
} elseif ($num > 1) { //es wurde mehr als einen Datenbankeintrag für die Anfrage gefunden...
    echo "<p>Oh oh! Big problems! Es wurde mehr als ein Eintrag für coll_id $coll_id gefunden!</p>";
} else { // es gibt, wie es sich gehört, nur einen Eintrag
    $mrow = $opus->fetch_row($res);
    $root_id = $mrow[0];
    $lft = $mrow[1];
    $rgt = $mrow[2];
    $opus->free_result($res);
    //	echo "<p>Root_id ist $root_id, lft ist $lft, rgt ist $rgt</p>";
    // Eine Variable, $move muss berechnet werden, die angibt, um welche Werte die $lft / $rgt-Attribute der Folgeknoten vermindert werden müssen
    $move = floor(($rgt-$lft) /2);
    $move = 2*(1+$move);
    //	echo "<p>Move ist $move</p>";
    // Jetzt kann der Teilbaum entfernt werden
    $query_delete = "DELETE FROM collections
						WHERE root_id = '$root_id'
						AND lft BETWEEN '$lft' AND '$rgt';";
    $query_update1 = "UPDATE collections
						SET lft = lft - $move
						WHERE root_id = '$root_id' and lft > '$rgt';";
    $query_update2 = "UPDATE collections
						SET rgt = rgt - $move
						WHERE root_id = '$root_id' AND rgt > '$rgt';";
    $res = $opus->query("LOCK TABLES collections WRITE");
    $delete_ok = $res = $opus->query($query_delete);
    $update1_ok = $res = $opus->query($query_update1);
    $update2_ok = $res = $opus->query($query_update2);
    $res = $opus->query("UNLOCK TABLES");
    if ($delete_ok == 1 && $update1_ok == 1 && $update2_ok == 1) {
        echo "<p>Die Collection wurde aus der Datenbank entfernt</p>";
    } else {
        echo "<p>Die Collection konnte nicht aus der Datenbank entfernt werden. ($insert_ok/$update_ok)</p>";
    }
}
// Nun müssen noch eventuelle Verbindungen von Dokumenten mit der Collection entfernt werden
// Zuerst aus der Tabelle opus_coll
$stat_opus_coll = $opus->query("DELETE FROM $opus_coll WHERE coll_id = '$coll_id'");
// Dann aus der Tabelle temp_coll
$stat_temp_coll = $opus->query("DELETE FROM $temp_coll WHERE coll_id = '$coll_id'");
// Fertig!
echo "<p><a href=\"$url/admin/collections.php?la=$la\">Zur&uuml;ck zur Collection-Administration</a></p>";
$opus->close($sock);
$design->foot("collections_delete.$php", $la);
?>

