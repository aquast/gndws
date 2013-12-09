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
 * @version     $Id: collections_verschieben_3.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_collections_verschieben_3");
$ueberschrift = $opus->value("ueberschrift_collections_verschieben_3");
include ("../../lib/stringValidation.php");
$coll_to = $_REQUEST['coll_to'];
if (!_is_valid($coll_to, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_to");
}
$hierarchie = $_REQUEST['hierarchie'];
if (!_is_valid($hierarchie, 0, 10, "[a-z]*")) {
    die("Fehler in Parameter hierarchie");
}
$position = $_REQUEST['position'];
if (!_is_valid($position, 0, 10, "[a-z]*")) {
    die("Fehler in Parameter position");
}
$coll_move = $_REQUEST['coll_move'];
if (!_is_valid($coll_move, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_move");
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
if ($coll_to == "") {
    echo "<p>Bitte w&auml;hlen Sie eine Ziel-Collection aus. </p><p>Bitte mit dem Backbutton zur&uuml;ck</p>";
} else {
    // Funktionen, die spaeter gebraucht werden
    function finde_parent_coll_id($coll_id) {
        global $opus;
        // Folgende query gibt alle Eltern, angefangen von sich selbst nach oben
        $query = "SELECT a.coll_id, (a.rgt - a.lft) AS height
                    FROM collections AS a, collections AS b
                    WHERE b.lft BETWEEN a.lft AND a.rgt
                    AND b.rgt BETWEEN a.lft AND a.rgt
                    AND b.coll_id = '$coll_id'
                    ORDER BY height ASC;";
        $res = $opus->query($query);
        $num = $opus->num_rows($res);
        if ($num == 0) {
            return "problem";
        } else {
            $opus->fetch_row($res); // Das erste Ergebnis ist die coll_id selbst. Eltern ist die zweite Reihe
            $mrow = $opus->fetch_row($res);
            return $mrow[0];
        }
        $opus->free_result($res);
    }
    // Hauptprogramm
    if ($coll_to == '1' && $hierarchie == 'oben') { // Ein Wurzelelement soll ueber dem Wurzelelement erzeugt werden. Geht nicht
        echo "<p>Sie wollen ein Wurzelelement &uuml;ber dem Wurzelelement erzeugen oder verschieben. Das geht leider nicht. Bitte gehen Sie mit dem Back-button zur&uuml;ck und &auml;ndern Sie ihre Einstellungen</p>\n";
    } elseif ($coll_to == '1' && $hierarchie == "gleich") { // Ein Element soll auf gleicher Ebene wie das Wurzelelement erzeugt werden. geht nicht.
        echo "<p>Es kann kein Element auf gleicher Ebene wie das Wurzelelement erzeugt /verschoben werden. Bitte gehen Sie mit dem Back-Button zur&uuml;ck und &auml;ndern Sie ihre Einstellungen</p>\n";
    } else {
        if ($hierarchie == "oben" || $hierarchie == "unten") { // $position ist nur fuer die hierarchie "gleich" wichtig
            $position = "";
        }
        if ($position == "vor" && $hierarchie == "unten") {
            echo "<p>Die Relation \"Davor\" mit Hierarchieebene \"Eine Ebene tiefer\" macht keinen Sinn. Bitte gehen Sie mit dem Back-button zur&uuml;ck und &auml;ndern Sie ihre Einstellungen</p>\n";
        } elseif ($position == "nach" && $hierarchie == "oben") {
            echo "<p>Die Relation \"Danach\" mit Hierarchieebene \"Eine Ebene h&ouml;her\" macht keinen Sinn. Bitte gehen Sie mit dem Back-button zur&uuml;ck und &auml;ndern Sie ihre Einstellungen</p>\n";
        } else {
            // Alle Informationen von der zu verschiebenden Hierarchie muss aus der Datenbank geholt werden
            $query = "SELECT * FROM collections WHERE coll_id = '$coll_move';";
            $res = $opus->query($query);
            $num = $opus->num_rows($res);
            if ($num == 1) {
                $mrow = $opus->fetch_row($res);
                $move_coll_id = $mrow[0];
                $move_root_id = $mrow[1];
                $move_coll_name = $mrow[2];
                $move_lft = $mrow[3];
                $move_rgt = $mrow[4];
                // Wir checken, ob es bereits eine Collection mit demselben Namen auf der Hierarchieebene, auf die verschoben wird, gibt.
                $check = 0;
                // Das Problem ist: wir kennen $coll_to und wir kennen $hierarchie. $hierarchie bestimmt, wo in der Hierarchie die Collection in Beziehung zu coll_to hin verschoben werden soll.
                // Wir brauchen den Elternknoten
                if ($hierarchie == "unten") {
                    $parent_id = $coll_to;
                } // Die einfachste Variante
                elseif ($hierarchie == "gleich") { // Elternknoten ist ein Knoten drueber
                    $parent_id = finde_parent_coll_id($coll_to);
                } elseif ($hierarchie == "oben") { // Elternknoten ist zwei Knoten drueber
                    $parent_id = finde_parent_coll_id($coll_to); // ermittelt parent_id
                    $parent_id = finde_parent_coll_id($parent_id); // Ermittelt Parent_id von der Parent_id
                    
                }
                // Nun checken wir, ob es eine andere Collection mit demselben Namen unter derselben parent_id gibt
                // Erst finden wir parent_lft und parent_rgt der zu verschiebenden coll_id raus
                $reslr = $opus->query("SELECT lft, rgt FROM collections WHERE coll_id = $parent_id");
                $mrowlr = $opus->fetch_row($reslr);
                $parent_lft = $mrowlr[0];
                $parent_rgt = $mrowlr[1];
                $opus->free_result($reslr);
                $query = "SELECT coll1.coll_id, coll1.coll_name, 
                        IF (coll1.coll_id = coll1.root_id,
                        round( (coll1.rgt - 2) / 2, 0),
                        round( ( (coll1.rgt - coll1.lft - 1) / 2), 0)
                    ) AS children,
                    COUNT(*) AS level 
                    FROM collections as coll1, 
                    collections as coll2 
                    WHERE coll1.root_id = 1 
                    AND coll2.root_id = 1 
                    AND coll1.lft BETWEEN $parent_lft +1 AND $parent_rgt -1
                    GROUP BY coll1.lft;";
                $res = $opus->query($query);
                $num = $opus->num_rows($res);
                if ($num > 0) { // Unter dem Elternknoten gibt es einen Hierarchiebaum
                    // Wir checken, ob es den neuen Namen schon auf gleicher Ebene gibt.
                    $i = 0;
                    $check = 0;
                    while ($i < $num) {
                        $mrow = $opus->fetch_row($res);
                        $coll_id_sibling = $mrow[0];
                        $coll_name_sibling = $mrow[1];
                        $children = $mrow[2];
                        $level = $mrow[3];
                        if ($children) {
                            if ($move_coll_name == $coll_name_sibling) {
                                $check = 1;
                            }
                            $n = 0;
                            while ($n < $children) { // Hier werden die Kinder einfach ignoriert
                                $mrow = $opus->fetch_row($res);
                                $n++;
                                $i++;
                            }
                            $i++;
                        } else {
                            if ($move_coll_name == $coll_name_sibling && $hierarchie != "gleich") {
                                $check = 1;
                            }
                            $i++;
                        }
                    }
                }
                if ($check == 1) {
                    echo "<p>Es gibt bereits eine Collection auf der Hierarchieebene auf die verschoben wird, die denselben Namen wie diese Collection hat. Bitte &auml;ndern sie zuerst den Namen der Collection, die sie verschieben wollen.</p>\n";
                    echo "<p><a href=\"collections_umbenennen.php?la=$la\">Collection umbenennen</a></p>";
                } else {
                    // Zuerst loeschen wir den Eintrag zu coll_move
                    // Eine Variable, $move, muss berechnet werden. Sie gibt an, umwelche Werte die $lft / $rgt
                    // Variablen der Folgeknoten gemindert werden muessen.
                    $move = floor(($move_rgt-$move_lft) /2);
                    $move = 2*(1+$move);
                    // Jetzt koennen wir die zu verschiebende Collection entfernen
                    $query_delete = "DELETE FROM collections
                                        WHERE root_id = '$move_root_id'
                                        AND lft BETWEEN '$move_lft' AND '$move_rgt';";
                    $query_update1 = "UPDATE collections
                                        SET lft = lft - $move
                                        WHERE root_id = '$move_root_id' and lft > '$move_rgt';";
                    $query_update2 = "UPDATE collections
                                        SET rgt = rgt - $move
                                        WHERE root_id = '$move_root_id' AND rgt > '$move_rgt';";
                    $res1 = $opus->query("LOCK TABLES collections WRITE");
                    $delete_ok = $res1 = $opus->query($query_delete);
                    $update1_ok = $res1 = $opus->query($query_update1);
                    $update2_ok = $res1 = $opus->query($query_update2);
                    $res1 = $opus->query("UNLOCK TABLES");
                    if ($delete_ok == 1 && $update1_ok == 1 && $update2_ok == 1) {
                        echo "<p>Schritt 1 von 2 ok</p>";
                    } else {
                        echo "<p>Die Collection konnte nicht aus der Datenbank entfernt werden. ($insert_ok/$update_ok)</p>";
                    }
                    // Nun holen wir Information zu der Hierarchie zu der verschoben wird aus der Datenbank
                    $query = "SELECT * FROM collections WHERE coll_id = '$coll_to';";
                    $res2 = $opus->query($query);
                    $num = $opus->num_rows($res2);
                    if ($num == 1) {
                        $mrow = $opus->fetch_row($res2);
                        $to_coll_id = $mrow[0];
                        $to_root_id = $mrow[1];
                        $to_coll_name = $mrow[2];
                        $to_lft = $mrow[3];
                        $to_rgt = $mrow[4];
                        // Nun erstellen wir die verschiedenen Update/Insert Queries, je nach Konfiguration
                        if ($hierarchie == "unten") {
                            // Einfach einfuegen wie ein normales einfuegen als Kind in Relation zu $coll_to
                            $query_update1 = "UPDATE collections
                                            SET lft = lft + 2
                                            WHERE root_id = '$to_root_id' 
                                            AND lft > $to_rgt
                                            AND rgt >= $to_rgt;";
                            $query_update2 = "UPDATE collections
                                            SET rgt = rgt + 2
                                            WHERE root_id = '$to_root_id' 
                                            AND rgt >= $to_rgt;";
                            $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
                                            VALUES( $move_root_id, '$move_coll_name', $to_rgt, $to_rgt + 1 );";
                        } elseif ($hierarchie == "gleich" || $hierarchie == "oben") {
                            // Wir emitteln coll_id, lft und rgt von Eltern und Grosselternhierarchie von $to_coll_id
                            $query = "SELECT v.coll_id, v.lft, v.rgt, v.root_id
                                        FROM collections v, collections s
                                        WHERE s.lft BETWEEN v.lft AND v.rgt
                                        AND s.coll_id = $to_coll_id
                                        ORDER BY v.lft DESC;";
                            $res3 = $opus->query($query);
                            $num = $opus->num_rows($res3);
                            $mrow = $opus->fetch_row($res3); // dies ist die Information zu dem to_element. Kann ignoriert werden
                            $mrow = $opus->fetch_row($res3); // dies ist die Information zu dem Elternelement
                            $eltern_coll_id = $mrow[0];
                            $eltern_lft = $mrow[1];
                            $eltern_rgt = $mrow[2];
                            $eltern_root_id = $mrow[3];
                            $opus->free_result($res3);
                            if ($hierarchie == "gleich") { // Die verschobene Hierarchie wird ein Bruderelement des to_Elements
                                if ($position == "vor") { // Eintrag als Bruder links to_Element
                                    $query_update1 = "UPDATE collections
                                        SET lft = lft + 2
                                        WHERE root_id = '$to_root_id' AND lft >= $to_lft;";
                                    $query_update2 = "UPDATE collections
                                        SET rgt = rgt + 2
                                        WHERE root_id = '$to_root_id' AND rgt > $to_lft;";
                                    $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
                                        VALUES( $to_root_id, '$move_coll_name', $to_lft, $to_lft + 1 );";
                                } elseif ($position == "nach") { // Eintrag als Bruder rechts to_Element
                                    $query_update1 = "UPDATE collections
                                        SET lft = lft + 2
                                        WHERE root_id = '$to_root_id' AND lft > $to_rgt;";
                                    $query_update2 = "UPDATE collections
                                        SET rgt = rgt + 2
                                        WHERE root_id = '$to_root_id' AND rgt > $to_rgt;";
                                    $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
                                        VALUES( $to_root_id, '$move_coll_name', $to_rgt + 1, $to_rgt + 2 );";
                                }
                            } elseif ($hierarchie == "oben") { // Eintrag als Kind vom Elternelement des to-Elements
                                $query_update1 = "UPDATE collections
                                    SET lft = lft + 2
                                    WHERE root_id = '$eltern_root_id' AND lft >= $eltern_lft;";
                                $query_update2 = "UPDATE collections
                                    SET rgt = rgt + 2
                                    WHERE root_id = '$eltern_root_id' AND rgt > $eltern_lft;";
                                $query_insert = "INSERT INTO collections( root_id, coll_name, lft, rgt )
                                    VALUES( $eltern_root_id, '$move_coll_name', $eltern_lft, $eltern_lft + 1 );";
                            }
                        }
                        // So, nun haben wir alle Informationen. Jetzt koennen wir an der Datenbank rummachen
                        // als naechstes wird die Information der geloeschten Collection in die neue Stelle eingefuegt
                        $res4 = $opus->query("LOCK TABLES collections WRITE");
                        $update1_ok = $res4 = $opus->query($query_update1);
                        $update2_ok = $res4 = $opus->query($query_update2);
                        $insert_ok = $res4 = $opus->query($query_insert);
                        if ($insert_ok == 1 && $update1_ok == 1 && $update2_ok) {
                            echo "<p>Schritt 2 von 2 ok</p>";
                        } else {
                            echo "<p>Die Collection konnte nicht in der Datenbank aufgenommen werden ($insert_ok/$update1_ok/$update2_ok)</p>";
                        }
                        // Dann muessen wir noch eventuelle Verknuepfungen zwischen der Collection und dem Dokument updaten
                        // Als erstes muss die neue coll_id festgestellt werden. Es ist der groesste coll_id. Da die Tabelle noch gelocked ist, kann zwischenzeitlich niemand die Tabelle updaten
                        $res5 = $opus->query("SELECT MAX( coll_id ) FROM collections");
                        $mrow5 = $opus->fetch_row($res5);
                        $neue_coll_id = $mrow5[0];
                        $opus->free_result($res5);
                        $res6 = $opus->query("UNLOCK TABLES");
                        // Nun kann die neue Verknuepfung zwischen Dokument und Collection erstellt werden
                        $res7 = $opus->query("UPDATE $opus_coll SET coll_id = '$neue_coll_id' WHERE coll_id = '$move_coll_id'");
                        $res7 = $opus->query("UPDATE $temp_coll SET coll_id = '$neue_coll_id' WHERE coll_id = '$move_coll_id'");
                        // Und nun ist alles gut gelaufen!
                        
                    } else {
                        echo "<p>Es gibt ein Problem mit der Datenbankabfrage (Abfrage nach Hierarchie, zu der verschoben wird) $query. Sie hat $num Zeilen als Antwort.";
                    }
                    $opus->free_result($res2);
                }
            } else {
                echo "<p>Es gibt ein Problem mit der Datenbankabfrage (Abfrage nach Hierarchie, die verschoben werden soll) $query. Sie hat $num Zeilen als Antwort.";
            }
            $opus->free_result($res);
        }
    }
}
echo "<p><a href=\"$url/admin/collections.php?la=$la\">Zur&uuml;ck zur Collection-Administration</a></p>";
$design->foot("collections_verschieben_3.$php", $la);
?>
