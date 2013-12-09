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
 * @version     $Id: schriftenreihen.php 214 2007-05-17 10:28:15Z freudenberg $
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
$titel = $opus->value("titel_schriftenreihe");
$ueberschrift = $opus->value("ueberschrift_schriftenreihe");
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
$res = $opus->query("SELECT sr_id, name FROM schriftenreihen ORDER BY sr_id");
$num = $opus->num_rows($res);
if ($num > 0) {
    print ("<TABLE WIDTH=90% ALIGN=CENTER BORDER=1> \n");
    print ("    <TH VALIGN=TOP>ID </TH>\n");
    print ("    <TH VALIGN=TOP>NAME </TH>\n");
    print ("    <TH VALIGN=TOP>Aktion </TH>\n");
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        $sr_id = $mrow[0];
        $sr_name = htmlspecialchars($mrow[1]);
        print ("<TR>\n");
        print ("<TD VALIGN=TOP ALIGN=RIGHT>$sr_id</TD>");
        print ("<TD VALIGN=TOP>&nbsp; $sr_name</TD>");
        print ("<TD VALIGN=TOP ALIGN=CENTER><A HREF=\"./sr_aendern.php?sr_id=$sr_id&la=$la\">&Auml;ndern</A> | <A HREF=\"./sr_loeschen.php?sr_id=$sr_id&la=$la\">L&ouml;schen</A>");
        print ("</TR>\n");
    }
    print ("</TABLE>\n");
    $opus->free_result($res);
    $opus->close($sock);
}
print ("
<ul>
<li><a href=\"sr_hinzufuegen.php?la=$la\">Neue Schriftenreihe hinzuf&uuml;gen</a></li>
<li><a href=\"index.php?la=$la\">Zur&uuml;ck zur Admin Seite</a></li>
</ul>
");
$design->foot("schriftenreihen.$php", $la);
?>
