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
 * @version     $Id: collections_rename.php 214 2007-05-17 10:28:15Z freudenberg $
 */
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
$home = $opus->value("home");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_collections_rename");
$ueberschrift = $opus->value("ueberschrift_collections_rename");
include ("../../lib/stringValidation.php");
$coll_id = $_REQUEST['coll_id'];
if (!_is_valid($coll_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter coll_id");
}
$neuer_name = $_REQUEST['neuer_name'];
$neuer_name = _zeichen_loeschen("[`\n\r]", "", $neuer_name);
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
// Funktion, die gebraucht wird
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
//echo "<p>Id der Collection die umbenannt werden muss $coll_id</p>";
//echo "<p>Neuer Name: +${neuer_name}+</p>";
if ($neuer_name == "") {
    echo "<p>Es fehlt der neue Name f&uuml;r die Collection. Bitte mit Back-Button zur&uuml;ck!<p>\n";
} elseif ($coll_id == "") {
    echo "<p>Es fehlt die Collection, die umbenannt werden muss. Bitte mit Back-Button zur&uuml;ck!<p>\n";
} else {
    // Wir stellen fest, ob es denselben Namen auf der Hierarchie schon gibt
    $check = 0;
    // Wie ist die coll_id des Elternknotens?
    $parent_id = finde_parent_coll_id($coll_id);
    //	echo "parent_id ist $parent_id <br>";
    // Nun checken wir, ob es eine andere Collection mit demselben Namen unter derselben parent_id gibt
    // Erst finden wir parent_lft und parent_rgt der zu verschiebenden coll_id raus
    if ($parent_id != "") {
        $reslr = $opus->query("SELECT lft, rgt FROM collections WHERE coll_id = $parent_id;");
        $mrowlr = $opus->fetch_row($reslr);
        $parent_lft = $mrowlr[0];
        $parent_rgt = $mrowlr[1];
        $opus->free_result($reslr);
        // Und nun holen wir die anderen Collections auf derselben Ebene
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
                    if ($neuer_name == $coll_name_sibling) {
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
                    if ($neuer_name == $coll_name_sibling) {
                        $check = 1;
                    }
                    $i++;
                }
            }
        }
    }
    if ($check == 0) {
        $query = "UPDATE collections
							SET coll_name = '$neuer_name'
							WHERE coll_id = '$coll_id';";
        $res = $opus->query("LOCK TABLES collections WRITE");
        $res = $opus->query($query);
        $res = $opus->query("UNLOCK TABLES");
        echo "<p>Die Collection wurde umbenannt</p>";
    } else {
        echo "<h3>Es gibt bereits eine Collection mit dem Namen \"$neuer_name\" auf derselben Hierarchieebene. Bitte geben Sie einen anderen Namen:</h3>\n";
        print ("<form method=\"post\" action=\"$url/admin/collections_rename.$php\">\n
		<table border=0>\n	<tr><td colspan=2>\n
		<p>Bitte w&auml;hlen Sie die Collection aus, die umbenannt werden soll:</p>\n
		");
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
				AND coll1.lft BETWEEN coll2.lft AND coll2.rgt
				GROUP BY coll1.lft;";
        $res = $opus->query($query);
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $i = 0;
            while ($i < $num) {
                $mrow = $opus->fetch_row($res);
                $coll_id2 = $mrow[0];
                $coll_name = $mrow[1];
                $children = $mrow[2];
                $indent = str_repeat("&nbsp;&nbsp;", $mrow[3]);
                echo "$indent <input type=\"radio\" name=\"coll_id\" value=\"$coll_id2\" ";
                if ($coll_id2 == $coll_id) {
                    echo "checked";
                }
                echo ">$coll_name (Anzahl Untercollections: $children)<br>\n";
                $i++;
            }
        } else { // Vorhandene Einträge werden mit der query nicht gefunden
            echo "<p>Houston, we have a problem</p>";
        }
        $opus->free_result($res);
        print ("</td></tr>
		<tr>
		<td>Neuer Name der ausgew&auml;hlten Collection:</td>
		<td><input type=\"text\" name=\"neuer_name\" size=\"40\"></td>
		</tr>
		<tr>
		<td><input type=\"submit\" value=\"Abschicken\"></td>
		<td><input type=\"reset\" value=\"L&ouml;schen\"></td>
		</tr></table>
		<input type=\"hidden\" name=\"la\" value=\"$la\">
		</form> 
		");
    }
}
echo "<p><a href=\"$url/admin/collections.php?la=$la\">Zur&uuml;ck zur Collection-Administration</a></p>";
$opus->close($sock);
$design->foot("collections_rename.$php", $la);
?>
