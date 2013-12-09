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
 * @version     $Id: show_ddc.php 214 2007-05-17 10:28:15Z freudenberg $
 */
// Gibt die OPUS-DDC-Tabelle aus.
// Aufruf am besten aus einer anderen HTML-/PHP-Seite mit dem Link
// <a HREF="show_ddc.php" target="new">Link</a>
//
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
$projekt = $opus->value("projekt");
$titel = "DDC suchen in " . $projekt;
$ueberschrift = "DDC suchen in " . $projekt;
include ("../../lib/stringValidation.php");
$sort = $_REQUEST['sort'];
$sort = _zeichen_loeschen("[`\n\r]", "", $sort);
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
if (!$sort) {
    $sort = "nr"; // default-Sortierung nach nr (alternativ: sachgruppe, sachgruppe_en)
    
}
$res = $opus->query("SELECT * FROM sachgruppe_ddc_$la ORDER BY $sort");
$num = $opus->num_rows($res);
echo "<small>Mit Klick auf die entsprechende Tabellen&uuml;berschrift sortieren oder <strong>&lt;STRG-F&gt; zum Suchen</strong>.</small>\n";
echo "<table border=\"1\" cellpadding=\"2\" cellspacing=\"1\">\n";
echo "<tr>";
echo "<th>\n";
echo "Nr.\n";
echo "</th><th>\n";
echo "<a href=\"" . basename($SCRIPT_NAME) . "?sort=nr\">DDC-ID</a>\n";
echo "</th><th>\n";
echo "<a href=\"" . basename($SCRIPT_NAME) . "?sort=sachgruppe\">Sachgruppe</a>\n";
echo "</th><th>\n";
echo "<a href=\"" . basename($SCRIPT_NAME) . "?sort=sachgruppe_en\">Sachgruppe Englisch</a>\n";
echo "</th></tr>\n";
$i = 0;
while ($i < $num) {
    $i++;
    $row = $opus->fetch_row($res);
    echo "<tr><small>\n";
    $tmp_nr = "<td>" . $i . "</td>\n<td>" . $row[0] . "</td>\n";
    $tmp_sd = "<td>" . $row[1] . "</td>\n";
    $tmp_se = "<td>" . $row[2] . "</td>\n";
    echo $tmp_nr . $tmp_sd . $tmp_se;
    echo "</small></tr>\n";
}
echo "</table><p />\n";
$opus->free_result($res);
$design->foot("show_ddc.$php", $la);
?>
