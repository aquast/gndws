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
 * @version     $Id: sr_replace.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_sr_aendern");
$ueberschrift = $opus->value("ueberschrift_sr_aendern") . " " . $sr_id;
include ("../../lib/stringValidation.php");
$sr_id = $_REQUEST['sr_id'];
if (!_is_valid($sr_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter sr_id");
}
$new_name = $_REQUEST['new_name'];
$new_name = _zeichen_loeschen("[`\n\r]", "", $new_name);
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
if (strlen($new_name) < 1) {
    print "Kein Name angegeben";
} else {
    print ("<FONT COLOR=red>");
    $sock = $opus->connect();
    if ($sock < 0) {
        echo ("Error: $ERRMSG\n");
    }
    if ($opus->select_db() < 0) {
        echo ("Error: $ERRMSG\n");
    }
    print ("</FONT>");
    $stat = $opus->query("UPDATE schriftenreihen SET name = '$new_name' WHERE sr_id=$sr_id");
    if ($stat < 0) {
        print ("ERROR $ERRMSG <p>ID $sr_id konnte nicht ge&auml;ndert werden.");
    } else {
        print ("ID $sr_id in \"$new_name\" ge&auml;ndert \n");
    }
    $opus->close($sock);
}
print ("
<UL>
<LI><A HREF=\"schriftenreihen.php?la=$la\">Zur&uuml;ck</A></LI>
</UL>
");
$design->foot("sr_replace.$php", $la);
?>
