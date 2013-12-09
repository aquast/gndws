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
 * @version     $Id: collections_verschieben.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_collections_verschieben");
$ueberschrift = $opus->value("ueberschrift_collections_verschieben");
include ("../../lib/stringValidation.php");
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
// Zuerst machen wir eine Datenbankabfrage, ob es überhaupt schon Collections gibt, die man verschieben kann
$query = "SELECT * FROM collections;";
$res = $opus->query($query);
$num = $opus->num_rows($res);
if ($num <= 1) { // es gibt nur eine oder keine Collection; also nichts, was man verschieben kann
    echo "<p> Es gibt keine Collection, oder es gibt keine Collection, die man verschieben kann</p>";
} else { // es gibt Collections die man verschieben kann. Also kann's losgehen
    echo "<p>Hier k&ouml;nnen Sie eine bestehende Collection in der Hierarchie verschieben. Das bedeutet, dass Sie die Collection auf eine andere Stelle verschieben und/oder in eine Hierarchiestufe h&ouml;her oder tiefer schieben k&ouml;nnen. Es k&ouml;nnen nur Hierarchien <b>OHNE</b> Unterhierarchien verschoben werden.</p>";
    echo "<p>Bitte w&auml;hlen Sie die Collection aus, die Sie verschieben wollen:</p>";
    echo "<form method=\"post\" action=\"$url/admin/collections_verschieben_2.$php\">\n<table border=0>\n<tr>\n<td>";
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
            print ("$indent <input type=\"radio\" name=\"coll_move\" value=\"$coll_id\">$coll_name (Anzahl Untercollections: $children)<br>
			");
            $i++;
        }
    } else { // Vorhandene Einträge werden mit der query nicht gefunden
        echo "<p>Houston, we have a problem</p>";
    }
    $opus->free_result($res1);
    print ("</td></tr>
	<tr>
	<td><input type=\"submit\" value=\"Abschicken\"></td>
	<td><input type=\"reset\" value=\"L&ouml;schen\"></td>
	</tr></table>
	<input type=\"hidden\" name=\"la\" value=\"$la\">
	</form> 
	");
}
$opus->free_result($res);
echo "<p><a href=\"$url/admin/collections.php\">Zur&uuml;ck zur Collection-Administration</a></p>";
$design->foot("collections_verschieben.$php", $la);
?>

