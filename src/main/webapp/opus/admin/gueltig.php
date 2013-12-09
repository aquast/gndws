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
 * @version     $Id: gueltig.php 214 2007-05-17 10:28:15Z freudenberg $
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
$url = $opus->value("url");
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$email = $opus->value("email");
$opusmail = $opus->value("opusmail");
$titel = "G&uuml;ltigkeitspr&uuml;fung";
$ueberschrift = "G&uuml;ltigkeitspr&uuml;fung";
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
$time = time();
$res = $opus->query("SELECT source_opus from $opus_table where date_valid > 0 and date_valid < $time ");
$num = $opus->num_rows($res);
if ($num > 0) {
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        $source_opus = $mrow[0];
        $message = "Das Dokument mit der IDN $source_opus\n\n";
        $message = $message . "ist nicht mehr gueltig und kann geloescht werden.";
        mail($email, "IDN $source_opus nicht mehr gueltig", $message, "from: $opusmail");
    }
    $opus->free_result($res);
}
print ("Anzahl der nicht mehr g&uuml;ltigen Dokumente: $num");
$opus->close($sock);
$design->foot("gueltig.$php", $la);
?>

