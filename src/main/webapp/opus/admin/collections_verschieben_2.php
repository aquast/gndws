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
 * @version     $Id: collections_verschieben_2.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_collections_verschieben_2");
$ueberschrift = $opus->value("ueberschrift_collections_verschieben_2");
include ("../../lib/stringValidation.php");
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
// Wir checken, ob eine Hierarchie ausgewaehlt wurde
if ($coll_move == "") {
    echo "<p>Bitte Collection, die verschoben werden muss, ausw&auml;hlen!</p>";
    echo "<p><a href=\"$url/admin/collections_verschieben.php\">Zur&uuml;ck</a>";
} else {
    // Wir checken, ob eine Hierarchie ohne Unterhierarchien ausgewaehlt wurde
    $query = "SELECT lft, rgt FROM collections WHERE coll_id = '$coll_move';";
    $res = $opus->query($query);
    $mrow = $opus->fetch_row($res);
    $lft = $mrow[0];
    $rgt = $mrow[1];
    $diff = $rgt-$lft;
    if ($diff == 1) { // Es gibt keine Unterhierarchien
        echo "<p>Als n&auml;chstes muss die Collection ausgew&auml;hlt werden, vor oder hinter der die im Schritt 1 ausgew&auml;hlte Collection platziert werden soll</p>";
        echo "<form method=\"post\" action=\"$url/admin/collections_verschieben_3.$php\">\n<table border=0>\n<tr>\n<td colspan=2>";
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
        $res1 = $opus->query($query);
        $num = $opus->num_rows($res1);
        if ($num > 0) {
            $i = 0;
            while ($i < $num) {
                $mrow = $opus->fetch_row($res1);
                $coll_id = $mrow[0];
                $coll_name = $mrow[1];
                $children = $mrow[2];
                $indent = str_repeat("&nbsp;&nbsp;", $mrow[3]);
                if ($coll_id != $coll_move) { // Die in Schritt 1 ausgewaehlte Collection soll nicht wieder auswaehlbar sein
                    print ("$indent <input type=\"radio\" name=\"coll_to\" value=\"$coll_id\">$coll_name (Anzahl Untercollections: $children)<br>
                    ");
                } else { //
                    $indent = $indent . "&nbsp;&nbsp;";
                    print ("$indent &bull; $coll_name (Anzahl Untercollections: $children)<br>
                    ");
                }
                $i++;
            }
        } else { // Vorhandene Eintraege werden mit der query nicht gefunden
            echo "<p>Houston, we have a problem</p>";
        }
        $opus->free_result($res1);
        print ("</td></tr>
        <tr><td colspan=2>
        <p>In welcher Hierarchieebene soll die zu verschiebende Collection mit der hier ausgew&auml;hlten Collection stehen?</p>
        <input type=\"radio\" name=\"hierarchie\" value=\"gleich\" checked>Auf gleicher Ebene<br>
        <input type=\"radio\" name=\"hierarchie\" value=\"oben\">Eine Ebene h&ouml;her<br>
        <input type=\"radio\" name=\"hierarchie\" value=\"unten\">Eine Ebene tiefer
        </td></tr>
        <tr><td colspan=2>
        <p>Wenn auf gleicher Ebene wie die ausgesuchte Ebene: Bitte sagen Sie, wo die zu verschiebene Collection in Relation mit der hier ausgew&auml;hlten Collection stehen soll:</p>
        <input type=\"radio\" name=\"position\" value=\"vor\">Davor<br>
        <input type=\"radio\" name=\"position\" value=\"nach\" checked>Dahinter
        </td></tr>
        <tr>
        <td><input type=\"submit\" value=\"Abschicken\"></td>
        <td><input type=\"reset\" value=\"L&ouml;schen\"></td>
        </tr></table>
        <input type=\"hidden\" name=\"coll_move\" value=\"$coll_move\">
	<input type=\"hidden\" name=\"la\" value=\"$la\">
        </form> 
        ");
    } else { // Es gibt Unterhierarchien - kann nicht verschoben werden
        echo "<p>Die ausgew&auml;hlte Collection hat Untercollections und kann deswegen nicht verschoben werden.</p>
                <p>Mit Back-Button zur&uuml;ck";
    }
    $opus->free_result($res);
}
echo "<p><a href=\"$url/admin/collections.php\">Zur&uuml;ck zur Collection-Administration</a></p>";
$design->foot("collections_verschieben_2.$php", $la);
?>
