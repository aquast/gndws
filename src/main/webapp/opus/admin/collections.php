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
 * @version     $Id: collections.php 214 2007-05-17 10:28:15Z freudenberg $
 */
// Um Collections in MySQL zu verwenden, wird das Nested-Set-Modell benutzt.
// Bevor man die SQL-Anfragen aendert, sollte man auf jeden Fall mit diesem
// Modell vertraut sein. Es gibt einige Artikel darueber auf dem Internet.
# letzte �nderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
$home = $opus->value("home");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_collections");
$ueberschrift = $opus->value("ueberschrift_collections");
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
print ("
<h4>Funktionen zum Verwalten von Collections</h4>
<ul>
<li><a href=\"collections_anlegen.php?la=$la\">Collection anlegen</a></li>
<li><a href=\"collections_loeschen.php?la=$la\">Collection l&ouml;schen</a></li>
<li><a href=\"collections_umbenennen.php?la=$la\">Collection umbenennen</a></li>
<li><a href=\"collections_verschieben.php?la=$la\">Collections in der Hierarchie verschieben</a></li>
</ul>
 ");
// Als eine Uebersicht wird die Hierarchie einmal ausgegeben:
// Zuerst machen wir eine Datenbankabfrage und checken, ob es Collections gibt, und wenn ja, welche:
$query = "SELECT * FROM collections";
$res = $opus->query($query);
$num = $opus->num_rows($res);
if ($num > 0) { // Es gibt bereits Eintraege in der Tabelle collections
    echo "<h4>Vorhandene Collections:</h4>";
    echo "<p>";
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
            $coll_id = $mrow[0];
            $coll_name = $mrow[1];
            $children = $mrow[2];
            $indent = str_repeat("&nbsp;&nbsp;", $mrow[3]);
            echo "$indent &bull; $coll_name (Anzahl Untercollections: $children)<br>";
            $i++;
        }
        echo "</p>";
        $opus->free_result($res);
    } else { // Vorhandene Eintraege werden mit der query nicht gefunden
        echo "<p>Houston, we have a problem</p>";
    }
} else { // Es gibt noch keine Eintraege in der Tabelle collections
    echo "<p>Es gibt noch keine Collections.</p>";
}
echo "<p><a href=\"$url/admin/?la=$la\">Zur&uuml;ck zur Adminseite</a></p>";
$design->foot("collections.$php", $la);
?>