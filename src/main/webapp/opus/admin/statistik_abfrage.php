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
 * @version     $Id: statistik_abfrage.php 214 2007-05-17 10:28:15Z freudenberg $
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
$opus_table = $opus->value("opus_table");
$url = $opus->value("url");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_statistik_abfrage");
$ueberschrift = $opus->value("ueberschrift_statistik_abfrage");
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
print ("<FORM METHOD=GET ACTION=\"$url/admin/statistik.$php?la=$la\">

Statistik &uuml;ber die in $projekt ver&ouml;ffentlichten Dokumente");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
$res = $opus->query("SELECT date_creation,source_opus FROM $opus_table order by date_creation");
$num = $opus->num_rows($res);
$last = $num-1;
$opus->data_seek($res, 0);
$mrow = $opus->fetch_row($res);
$date1 = $mrow[0];
$jahr1 = date("Y", $date1);
$opus->data_seek($res, $last);
$mrow = $opus->fetch_row($res);
$date2 = $mrow[0];
$jahr2 = date("Y", $date2);
print ("
<P> 
Jahr:
<SELECT NAME=\"jahr\" SIZE=1>
");
$jahr = $jahr1;
while ($jahr <= $jahr2) {
    print ("<OPTION>$jahr");
    $jahr++;
}
print ("
</SELECT>
<P>
<input type=\"hidden\" name=\"la\" value=\"$la\">
<INPUT TYPE=\"submit\" VALUE=\"Abfrage starten\">
</FORM>
");
$design->foot("statistik_abfrage.$php", $la);
?>

