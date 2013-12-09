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
 * @version     $Id: collections_siblings.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_collections_siblings");
$ueberschrift = $opus->value("ueberschrift_collections_siblings");
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
// Erstmal testen, ob auch alle noetigen Informationen gekommen sind.
$m = 0;
if (!$coll_name) {
    echo "<p>Sie m&uuml;ssen der neuen Collection einen Namen geben!</p>";
    $m = 1;
}
if ($root == "no") {
    if (!$coll_before) {
        echo "<p>Bitte w&auml;hlen Sie eine Collection aus, hinter der die neue Collection erscheinen soll!</p>";
        $m = 1;
    }
}
if ($m == 1) {
    echo "<p>Bitte mit dem Back-Button zur&uuml;ck!</p>";
} else {
    if ($root == "yes") { // Es handelt sich um die Wurzel Collection, d.h. es gibt keine Geschwister
        echo "<p>Es gibt noch keine Collection</p>
		<p><form method=\"post\" action=\"$url/admin/collections_update.$php\">
		<input type=\"submit\" value=\"Abschicken\">
		<input type=\"hidden\" name=\"root\" value=\"yes\">
		<input type=\"hidden\" name=\"coll_name\" value=\"$coll_name\">
		<input type=\"hidden\" name=\"la\" value=\"$la\">
		</form></p>";
    } else {
        // Wir muessen ermitteln, ob die neue Collection Geschwister, d.h. Collections auf derselben Hierarchieebene hat
        // Zuerst muessen wir die Daten der Eltern-Collection abfragen
        $query = "SELECT root_id, lft, rgt FROM collections WHERE coll_id = '$coll_before';";
        //	echo "<p>$query</p>";
        $res = $opus->query($query);
        $mrow = $opus->fetch_row($res);
        $root_id = $mrow[0];
        $parent_lft = $mrow[1];
        $parent_rgt = $mrow[2];
        $opus->free_result($res);
        // Nun wollen wir den Hierarchiebaum ab des Elternknotens herausgeben
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
                    if ($coll_name == $coll_name_sibling) {
                        echo "<p>Dieser Name existiert schon auf gleicher Ebene. Bitte mit Back-Button zur&uuml;ck und einen anderen Namen geben.</p>\n";
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
                    if ($coll_name == $coll_name_sibling) {
                        echo "<p>Dieser Name existiert schon auf gleicher Ebene. Bitte mit Back-Button zur&uuml;ck und einen anderen Namen geben.</p>\n";
                        $check = 1;
                    }
                    $i++;
                }
            }
            if ($check == 0) { // Es gibt keine Collections mit gleichen Namen auf derselben Ebene
                #echo "<p>Keine Collection mit gleichem Namen</p>";
                $query2 = "SELECT coll1.coll_id, coll1.coll_name, 
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
                $res2 = $opus->query($query2);
                $num2 = $opus->num_rows($res2);
                echo "<p>Folgende Collections wurden auf der gleichen Ebene gefunden, in der Sie die neue Collection einf&uuml;gen m&ouml;chten. Bitte w&auml;hlen Sie die Collection aus, hinter oder vor der die neue Collection in die Hierarchie eingef&uuml;gt werden soll.</p>\n";
                echo "<p><form method=\"post\" action=\"$url/admin/collections_update.$php\">\n
				<input type=\"hidden\" name=\"root\" value=\"no\">\n
				<input type=\"hidden\" name=\"coll_name\" value=\"$coll_name\">\n
				<input type=\"hidden\" name=\"coll_before\" value=\"$coll_before\">
				<input type=\"hidden\" name=\"la\" value=\"$la\">\n";
                echo "<table border=0>\n";
                $l = 0;
                while ($l < $num2) {
                    $mrow2 = $opus->fetch_row($res2);
                    $coll_id_sibling2 = $mrow2[0];
                    $coll_name_sibling2 = $mrow2[1];
                    $children2 = $mrow2[2];
                    $level2 = $mrow2[3];
                    if ($children2) {
                        // Wir wollen nur Elemente, die sich auf derselben Hierarchieebene wie das einzufuegende Element
                        // befinden, herausgeben. Kinder von Elementen werden ignoriert
                        if ($l == $num2-1) {
                            print ("\n<tr><td><input type=\"radio\" name=\"coll_id_sibling\" value=\"$coll_id_sibling2\" checked>$coll_name_sibling2</td>\n</tr>
							");
                        } else {
                            print ("\n<tr><td><input type=\"radio\" name=\"coll_id_sibling\" value=\"$coll_id_sibling2\">$coll_name_sibling2</td>\n</tr>
							");
                        }
                        $m = 0;
                        while ($m < $children2) { // Hier werden die Kinder einfach ignoriert
                            $mrow2 = $opus->fetch_row($res2);
                            $coll_id2 = $mrow2[0];
                            $coll_name2 = $mrow2[1];
                            $m++;
                            $l++;
                        }
                        $l++;
                    } else { // Das sind Elemente ohne Kinder
                        if ($l == $num2-1) {
                            print ("\n<tr><td><input type=\"radio\" name=\"coll_id_sibling\" value=\"$coll_id_sibling2\" checked>$coll_name_sibling2</td>\n</tr>
							");
                        } else {
                            print ("\n<tr><td><input type=\"radio\" name=\"coll_id_sibling\" value=\"$coll_id_sibling2\">$coll_name_sibling2</td>\n</tr>
							");
                        }
                        $l++;
                    }
                }
                echo "<tr><td>&nbsp;</td></tr>
				<tr><td>
				<p>Position:</p>
				<input type=\"radio\" name=\"position\" value=\"vor\">Vor der ausgew&auml;hlten Collection<br>
				<input type=\"radio\" name=\"position\" value=\"nach\" checked>Nach der ausgew&auml;hlten Collection
				
				</td></tr>
				<tr><td><input type=\"submit\" value=\"Weiter\"></td></tr></table>
				</form></p>";
            }
        } else { // Unter dem Elternknoten werden keine Hierarchien gefunden. Dies ist die erste Kinds-Kategorie des Elternbaumes
            echo "<p>Es gibt noch keine Collections auf gleicher Ebene. Bitte auf folgenden Button klicken!</p>\n";
            echo "<p><form method=\"post\" action=\"$url/admin/collections_update.$php\">
			<input type=\"hidden\" name=\"root\" value=\"no\">
			<input type=\"hidden\" name=\"coll_name\" value=\"$coll_name\">
			<input type=\"hidden\" name=\"coll_before\" value=\"$coll_before\">
			<input type=\"submit\" value=\"weiter\">
			<input type=\"hidden\" name=\"la\" value=\"$la\">
			</form></p>";
        }
        $opus->free_result($res);
    }
}
$opus->close($sock);
$design->foot("collections_siblings.$php", $la);
?>
