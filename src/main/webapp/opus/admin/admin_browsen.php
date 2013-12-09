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
 * @version     $Id: admin_browsen.php 214 2007-05-17 10:28:15Z freudenberg $
 */
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$url = $opus->value("url");
$projekt = $opus->value("projekt");
include ("../../lib/stringValidation.php");
$table = $_REQUEST['table'];
if ($table != $opus_table && $table != $temp_table) {
    die("Fehler in Parameter table");
}
$suchfeld = $_REQUEST['suchfeld'];
if (!_is_valid($suchfeld, 0, 30, "[a-z0-9\.\_]*")) {
    die("Fehler in Parameter suchfeld");
}
$suchwert = $_REQUEST['suchwert'];
$suchwert = _zeichen_loeschen("[`\n\r]", "", $suchwert);
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$table_autor = $table . "_autor";
if ($suchfeld == "source_opus" || $suchfeld == "source_swb") {
    header("Location: $url/admin/aendern.$php?table=$table&suchfeld=$suchfeld&suchwert=$suchwert&la=$la");
} else {
    $titel_admin_browsen = "titel_" . $table . "_admin_browsen";
    $ueberschrift_admin_browsen = "ueberschrift_" . $table . "_admin_browsen";
    $titel = $opus->value("$titel_admin_browsen");
    $ueberschrift = $opus->value("$ueberschrift_admin_browsen");
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
    if ($suchfeld == "") {
        $res = $opus->query("SELECT title, source_opus, date_year FROM $table 
	         ORDER BY  source_opus");
    } else {
        if ($suchfeld == "creator_name") {
            $res = $opus->query("SELECT distinct t.title, t.source_opus, t.date_year FROM $table t, $table_autor ta where ta.creator_name like '$suchwert%' and t.source_opus = ta.source_opus ORDER BY  ta.creator_name");
        } else {
            $res = $opus->query("SELECT distinct title, source_opus, date_year FROM $table WHERE $suchfeld LIKE '%$suchwert%' ORDER BY title");
        }
    }
    $num = $opus->num_rows($res);
    if ($num > 0) {
        print ("<TABLE BORDER=1 CELLPADDING=5> \n");
        print ("    <TH VALIGN=TOP>$projekt IDN </TH>\n");
        print ("    <TH VALIGN=TOP>Titel </TH>\n");
        print ("    <TH VALIGN=TOP>Autor </TH>\n");
        print ("    <TH VALIGN=TOP>Jahr </TH>\n");
        $opus->data_seek($res, 0);
        $i = 0;
        while ($i < $num) {
            $i++;
            $mrow = $opus->fetch_row($res);
            $title = htmlspecialchars(($mrow[0]));
            $source_opus = $mrow[1];
            $date_year = $mrow[2];
            $creator_name = "&nbsp;";
            print ("<TR>\n<TD VALIGN=TOP>$source_opus</TD> \n");
            print ("<TD VALIGN=TOP><A HREF=\"$url/admin/aendern.$php?table=$table&suchfeld=source_opus&suchwert=$source_opus&la=$la\">$title</A></TD> \n");
            $autor = $opus->query("SELECT creator_name, reihenfolge FROM $table_autor where source_opus = $source_opus ORDER BY  reihenfolge");
            $num_autor = $opus->num_rows($autor);
            $j = 0;
            $creator_name = "";
            if ($num_autor > 0) {
                while ($j < $num_autor) {
                    $mrow_autor = $opus->fetch_row($autor);
                    $mrow_autor[0] = htmlspecialchars($mrow_autor[0]);
                    $creator_name = $creator_name . $mrow_autor[0] . "<BR>";
                    $j++;
                }
            }
            $opus->free_result($autor);
            print ("<TD VALIGN=TOP>$creator_name</TD> \n");
            print ("<TD VALIGN=TOP> $date_year</TD> \n");
            print ("</TR>\n");
        }
        print ("</TABLE> \n");
    } else {
        print ("Keine Dokumente gefunden.\n");
    }
    $opus->free_result($res);
    $opus->close($sock);
    $design->foot("admin_browsen.$php", $la);
}
?>

