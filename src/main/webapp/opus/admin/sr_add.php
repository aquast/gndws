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
 * @version     $Id: sr_add.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_sr_add");
$ueberschrift = $opus->value("ueberschrift_sr_add");
include ("../../lib/stringValidation.php");
$new_sr = $_REQUEST['new_sr'];
$new_sr = _zeichen_loeschen("[`\n\r]", "", $new_sr);
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
print ("<FONT COLOR=red>");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
$res = $opus->query("SELECT name FROM schriftenreihen WHERE name='$new_sr'");
$num = $opus->num_rows($res);
$opus->free_result($res);
if ($num > 0) {
    print ("Die Schriftenreihe \"$new_sr\" existiert bereits");
} else {
    $stat = $opus->query("INSERT INTO schriftenreihen (name) VALUES ('$new_sr')");
    if ($stat < 0) {
        print ("ERROR $ERRMSG <p>ID $new_sr konnte nicht hinzugef&uuml;gt werden.");
    } else {
        print ("Schriftenreihe \"$new_sr\" hinzugef&uuml;gt \n");
    }
}
$opus->close($sock);
print ("
<UL>
<LI><A HREF=\"schriftenreihen.php?la=$la\">Zur&uuml;ck</A></LI>
</UL>
");
$design->foot("sr_add.$php", $la);
?>
