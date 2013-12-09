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
 * @version     $Id: collections_update.php 214 2007-05-17 10:28:15Z freudenberg $
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
$url = $opus->value("url");
$home = $opus->value("home");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_collections_update");
$ueberschrift = $opus->value("ueberschrift_collections_update");
include ("../../lib/stringValidation.php");
$root = $_REQUEST['root'];
if ($root != "yes" && $root != "no") {
    die("Fehler in Parameter root");
}
$coll_name = $_REQUEST['coll_name'];
$coll_name = _zeichen_loeschen("[`\n\r]", "", $coll_name);
$coll_before = $_REQUEST['coll_before'];
if (!_is_valid($coll_before, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_before");
}
$coll_id_sibling = $_REQUEST['coll_id_sibling'];
if (!_is_valid($coll_id_sibling, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_id_sibling");
}
$position = $_REQUEST['position'];
if (!_is_valid($position, 0, 10, "[a-z]*")) {
    die("Fehler in Parameter position");
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
if ($root == "yes") { // Dies ist der allererste Eintrag in die Tabelle collections
    $query_insert = "INSERT INTO collections( coll_name, lft, rgt )
						VALUES( \"$coll_name\", 1, 2 );";
    $query_update = "UPDATE collections
						SET root_id = LAST_INSERT_ID()
						WHERE coll_id = LAST_INSERT_ID();";
    $res = $opus->query("LOCK TABLES collections WRITE");
    $insert_ok = $res = $opus->query($query_insert);
    $update_ok = $res = $opus->query($query_update);
    $res = $opus->query("UNLOCK TABLES");
    if ($insert_ok == 1 && $update_ok == 1) {
        echo "<p>Die Collection wurde in der Datenbank aufgenommen</p>";
    } else {
        echo "<p>Die Collection konnte nicht in der Datenbank aufgenommen werden. ($insert_ok/$update_ok)</p>";
    }
} elseif ($root == "no") { // es gibt schon Einträge in der Datenbank
    if ($position && $coll_id_sibling) { // es gibt Geschwister, die berücksichtigt werden müssen
        $root_id = 1;
        // Wir müssen lft und rgt der Geschwister-Collection ermitteln
        $query = "SELECT lft, rgt FROM collections WHERE coll_id = \"$coll_id_sibling\";";
        $res = $opus->query($query);
        $mrow = $opus->fetch_row($res);
        $lft_sibling = $mrow[0];
        $rgt_sibling = $mrow[1];
        $opus->free_result($res);
        if ($position == "vor") { // Die neue Collection soll über dem Sibling stehen
            $query_update1 = "UPDATE collections
								SET lft = lft + 2
								WHERE root_id = '$root_id' AND lft >= $lft_sibling;";
            $query_update2 = "UPDATE collections
								SET rgt = rgt + 2
								WHERE root_id = '$root_id' AND rgt > $lft_sibling;";
            $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
								VALUES( $root_id, '$coll_name', $lft_sibling, $lft_sibling + 1 );";
        } elseif ($position == "nach") { // Die neue Collection soll unter dem Sibling stehen
            $query_update1 = "UPDATE collections
								SET lft = lft + 2
								WHERE root_id = '$root_id' AND lft > $rgt_sibling;";
            $query_update2 = "UPDATE collections
								SET rgt = rgt + 2
								WHERE root_id = '$root_id' AND rgt > $rgt_sibling;";
            $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
								VALUES( $root_id, '$coll_name', $rgt_sibling + 1, $rgt_sibling + 2 );";
        } else {
            echo "<p>Was für ein seltsamer Wert für \$position!!! $position</p>";
        }
    } else { // es gibt keine Geschwister
        // Nun müssen wir lft und rgt der Collection darüber ermitteln
        $query = "SELECT lft, rgt FROM collections WHERE coll_id = \"$coll_before\";";
        $res = $opus->query($query);
        $num = $opus->num_rows($res);
        if ($num <= 0) {
            echo "<p>Warum werden die Werte nicht gefunden? Sie muessen da sein. Query ist: $query</p>";
        } elseif ($num > 1) {
            echo "<p>Es gibt einen schwerwiegenden Fehler mit der Datenbank. Bitte checken!</p>";
        } else {
            $mrow = $opus->fetch_row($res);
            $lft = $mrow[0];
            $rgt = $mrow[1];
            $root_id = 1;
            $query_update1 = "UPDATE collections
								SET lft = lft + 2
								WHERE root_id = $root_id
								AND lft > $rgt
								AND rgt >= $rgt;";
            $query_update2 = "UPDATE collections
								SET rgt = rgt + 2
								WHERE root_id = $root_id
								AND rgt >= $rgt;";
            $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
								VALUES( \"$root_id\", \"$coll_name\", $rgt, $rgt + 1 );";
        }
        $opus->free_result($res);
    }
    // Die Queries werden ausgeführt
    $res = $opus->query("LOCK TABLES collections WRITE");
    $update1_ok = $res = $opus->query($query_update1);
    $update2_ok = $res = $opus->query($query_update2);
    $insert_ok = $res = $opus->query($query_insert);
    $res = $opus->query("UNLOCK TABLES");
    if ($insert_ok == 1 && $update1_ok == 1 && $update2_ok) {
        echo "<p>Die Collection wurde in der Datenbank aufgenommen</p>";
    } else {
        echo "<p>Die Collection konnte nicht in der Datenbank aufgenommen werden. ($insert_ok/$update1_ok/$update2_ok)</p>";
    }
}
echo "<p><a href=\"$url/admin/collections_anlegen.php?la=$la\">Neue Collection anlegen</a></p>";
echo "<p><a href=\"$url/admin/collections.php?la=$la\">Zur&uuml;ck zur Collection-Administration</a></p>";
$opus->close($sock);
$design->foot("collections_update.$php", $la);
?>

