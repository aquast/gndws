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
 * @version     $Id: temp_browsen.php 214 2007-05-17 10:28:15Z freudenberg $
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
$temp_table = $opus->value("temp_table");
$temp_table_autor = $temp_table . "_autor";
$titel = $opus->value("titel_temp_browsen");
$ueberschrift = $opus->value("ueberschrift_temp_browsen");
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
print ("<FONT COLOR=red>");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
$res = $opus->query("SELECT t.title, t.source_opus, t.date_year, t.status, r.dokumentart FROM $temp_table as t, resource_type_$la as r where r.typeid = t.type ORDER BY source_opus");
$num = $opus->num_rows($res);
if ($num > 0) {
    print ("<TABLE BORDER=1> \n");
    print ("    <TH VALIGN=TOP>Status </TH>\n");
    print ("    <TH VALIGN=TOP>TEMP-IDN </TH>\n");
    print ("    <TH VALIGN=TOP>Titel </TH>\n");
    print ("    <TH VALIGN=TOP>Autor </TH>\n");
    print ("    <TH VALIGN=TOP>Jahr </TH>\n");
    print ("    <TH VALIGN=TOP>Dokumentart </TH>\n");
    print ("    <TR> \n");
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        $titel = htmlspecialchars($mrow[0]);
        $source_opus = $mrow[1];
        $date_year = $mrow[2];
        $status = $mrow[3];
        $dokumentart = $mrow[4];
        $creator_name = "&nbsp;";
        print ("<TD VALIGN=TOP>$status</TD>");
        print ("<TD VALIGN=TOP>$source_opus</TD>");
        print ("<TD VALIGN=TOP>");
        print ("<A HREF=\"$url/admin/aendern.$php?suchfeld=source_opus&suchwert=$source_opus&table=temp\">$titel</A></TD>\n");
        $res2 = $opus->query("SELECT creator_name, reihenfolge FROM $temp_table_autor where source_opus = '$source_opus' ORDER BY  reihenfolge");
        $num2 = $opus->num_rows($res2);
        if ($num2 > 0) {
            $mrow2 = $opus->fetch_row($res2);
            $creator_name = htmlspecialchars($mrow2[0]);
        }
        $opus->free_result($res2);
        if ($num2 > 1) {
            $creator_name = $creator_name . " et al.";
        }
        print ("<TD VALIGN=TOP>$creator_name</TD>");
        print ("<TD VALIGN=TOP>$date_year</TD>");
        print ("<TD VALIGN=TOP>$dokumentart</TD>");
        print ("</TR><TR>\n ");
    }
    print ("</TR></TABLE> \n");
} else {
    print ("Keine Dokumente gefunden.\n");
}
$opus->free_result($res);
$opus->close($sock);
$design->foot("temp_browsen.$php", $la);
?>

