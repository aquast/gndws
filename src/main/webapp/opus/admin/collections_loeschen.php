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
 * @version     $Id: collections_loeschen.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_collections_loeschen");
$ueberschrift = $opus->value("ueberschrift_collections_loeschen");
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
// Zuerst machen wir eine Datenbankabfrage und checken, ob es Collections gibt, und wenn ja, welche:
$query = "SELECT * FROM collections";
$res = $opus->query($query);
$num = $opus->num_rows($res);
if ($num > 0) { // Es gibt bereits Einträge in der Tabelle collections
    print ("<form method=\"post\" action=\"$url/admin/collections_delete.$php\">
	<table border=0>	<tr><td colspan=2>
	<p>Bitte w&auml;hlen Sie die Collection aus, die gel&ouml;scht werden soll</p>
	<p>
	<p><b>Achtung: Alle Unter-Collectionen der Collection werden auch gel&ouml;scht!</b></p>
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
    $res2 = $opus->query($query);
    $num = $opus->num_rows($res2);
    if ($num > 0) {
        $i = 0;
        while ($i < $num) {
            $mrow = $opus->fetch_row($res2);
            $coll_id = $mrow[0];
            $coll_name = $mrow[1];
            $children = $mrow[2];
            $indent = str_repeat("&nbsp;&nbsp;", $mrow[3]);
            print ("$indent <input type=\"radio\" name=\"coll_id\" value=\"$coll_id\">$coll_name (Anzahl Untercollections: $children)<br>
			");
            $i++;
        }
    } else { // Vorhandene Einträge werden mit der query nicht gefunden
        echo "<p>Houston, we have a problem</p>";
    }
    $opus->free_result($res2);
    print ("</td></tr>
	<tr>
	<td><input type=\"submit\" value=\"Abschicken\"></td>
	<td><input type=\"reset\" value=\"L&ouml;schen\"></td>
	</tr></table>
	<input type=\"hidden\" name=\"la\" value=\"$la\">
	</form> 
	");
} else { // Es gibt noch keine Einträge in der Tabelle collections
    print ("<p>Es gibt keine Collection, die man l&ouml;schen kann</p>
	");
}
$opus->free_result($res);
$opus->close($sock);
$design->foot("collections_loeschen.$php", $la);
?>

