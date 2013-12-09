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
 * @version     $Id: urn_berechnen.php 214 2007-05-17 10:28:15Z freudenberg $
 */
//Programm berechnet Pruefziffer der vollstaendigen NBN fuer das Projekt
//CARMEN-AP4
//an Der Deutschen Bibliothek. Die verbale Beschreibung des Algorithmus zur
//Berechnung der Pruefziffer ist
//aus dem Dokument 'NBNPruefziffer.doc' ersichtlich.
//
//Autor: Kathrin Schroeder
//Stand: 12.07.2001
//geaendert fuer OPUS: Annette Maile
// letzte Aenderung:
//
// 22.4.2005, Annette Maile, UB Stuttgart
// Auslagerung Design in lib/design.php
// In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$url = $opus->value("url");
$titel = $opus->value("titel_urn_berechnen");
$ueberschrift = $opus->value("ueberschrift_urn_berechnen");
$db = $opus->value("db");
$table = $opus->value("opus_table");
$projekt = $opus->value("projekt");
$nbn = $opus->value("nbn");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
# A. Maile, 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$nbn = $nbn . $source_opus;
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
$NBN = strtolower($nbn);
if (preg_match("/\s/", $NBN) || !preg_match("/\:/", $NBN) || !preg_match("/\-/", $NBN) || preg_match("/[ä,ö,ü,ß]/", $NBN) || preg_match("/[_,;,,,.,+,*,~,#,',`,´,=,),(,\/,\&,%,\$,§,\",\!,°,\^,\\,\},\{,\],\[,\<,\>,\|]/", $NBN) || !preg_match("/^urn:nbn:de:/", $NBN)) {
?>
<script language="JavaScript">
window.alert
("Geben Sie bitte eine gültige NBN ein!");
</script>
<?php
} else {
    $NBN = strtolower($NBN);
    $zaehl = strlen($NBN);
    for ($i = 0;$i <= $zaehl;$i++) {
        $char = substr($NBN, $i, 1);
        if (preg_match("/9/", $char)) {
            $NBNK.= preg_replace("/9/", "41", $char);
        }
        if (preg_match("/8/", $char)) {
            $NBNK.= preg_replace("/8/", "9", $char);
        }
        if (preg_match("/7/", $char)) {
            $NBNK.= preg_replace("/7/", "8", $char);
        }
        if (preg_match("/6/", $char)) {
            $NBNK.= preg_replace("/6/", "7", $char);
        }
        if (preg_match("/5/", $char)) {
            $NBNK.= preg_replace("/5/", "6", $char);
        }
        if (preg_match("/4/", $char)) {
            $NBNK.= preg_replace("/4/", "5", $char);
        }
        if (preg_match("/3/", $char)) {
            $NBNK.= preg_replace("/3/", "4", $char);
        }
        if (preg_match("/2/", $char)) {
            $NBNK.= preg_replace("/2/", "3", $char);
        }
        if (preg_match("/1/", $char)) {
            $NBNK.= preg_replace("/1/", "2", $char);
        }
        if (preg_match("/0/", $char)) {
            $NBNK.= preg_replace("/0/", "1", $char);
        }
        if (preg_match("/a/", $char)) {
            $NBNK.= preg_replace("/a/", "18", $char);
        }
        if (preg_match("/b/", $char)) {
            $NBNK.= preg_replace("/b/", "14", $char);
        }
        if (preg_match("/c/", $char)) {
            $NBNK.= preg_replace("/c/", "19", $char);
        }
        if (preg_match("/d/", $char)) {
            $NBNK.= preg_replace("/d/", "15", $char);
        }
        if (preg_match("/e/", $char)) {
            $NBNK.= preg_replace("/e/", "16", $char);
        }
        if (preg_match("/f/", $char)) {
            $NBNK.= preg_replace("/f/", "21", $char);
        }
        if (preg_match("/g/", $char)) {
            $NBNK.= preg_replace("/g/", "22", $char);
        }
        if (preg_match("/h/", $char)) {
            $NBNK.= preg_replace("/h/", "23", $char);
        }
        if (preg_match("/i/", $char)) {
            $NBNK.= preg_replace("/i/", "24", $char);
        }
        if (preg_match("/j/", $char)) {
            $NBNK.= preg_replace("/j/", "25", $char);
        }
        if (preg_match("/k/", $char)) {
            $NBNK.= preg_replace("/k/", "42", $char);
        }
        if (preg_match("/l/", $char)) {
            $NBNK.= preg_replace("/l/", "26", $char);
        }
        if (preg_match("/m/", $char)) {
            $NBNK.= preg_replace("/m/", "27", $char);
        }
        if (preg_match("/n/", $char)) {
            $NBNK.= preg_replace("/n/", "13", $char);
        }
        if (preg_match("/o/", $char)) {
            $NBNK.= preg_replace("/o/", "28", $char);
        }
        if (preg_match("/p/", $char)) {
            $NBNK.= preg_replace("/p/", "29", $char);
        }
        if (preg_match("/q/", $char)) {
            $NBNK.= preg_replace("/q/", "31", $char);
        }
        if (preg_match("/r/", $char)) {
            $NBNK.= preg_replace("/r/", "12", $char);
        }
        if (preg_match("/s/", $char)) {
            $NBNK.= preg_replace("/s/", "32", $char);
        }
        if (preg_match("/t/", $char)) {
            $NBNK.= preg_replace("/t/", "33", $char);
        }
        if (preg_match("/u/", $char)) {
            $NBNK.= preg_replace("/u/", "11", $char);
        }
        if (preg_match("/v/", $char)) {
            $NBNK.= preg_replace("/v/", "34", $char);
        }
        if (preg_match("/w/", $char)) {
            $NBNK.= preg_replace("/w/", "35", $char);
        }
        if (preg_match("/x/", $char)) {
            $NBNK.= preg_replace("/x/", "36", $char);
        }
        if (preg_match("/y/", $char)) {
            $NBNK.= preg_replace("/y/", "37", $char);
        }
        if (preg_match("/z/", $char)) {
            $NBNK.= preg_replace("/z/", "38", $char);
        }
        if (preg_match("/-/", $char)) {
            $NBNK.= preg_replace("/-/", "39", $char);
        }
        if (preg_match("/\:/", $char)) {
            $NBNK.= preg_replace("/\:/", "17", $char);
        }
        //echo "NBNK: "."$NBNK<p>";
        
    }
    $NBNK = (string)$NBNK;
    $z = strlen($NBNK);
    //echo "Laenge der NBN: "."$z<p>";
    $URN = preg_split("//", $NBNK);
    //foreach($URN as $key => $wert)
    //echo "$key => $wert <p>";
    //echo "NBN-Array: "."$URN<p>";
    for ($ii = 1;$ii <= count($URN);$ii++) {
        $sum = $sum+($URN[$ii]*$ii);
    }
    //echo "Produktsumme: "."$sum<p>";
    $lz = $URN[$z];
    //echo "letzte Zahl der konvertierten NBN: "."$lz<p>";
    $quot = floor($sum/$lz);
    //echo "Quotient: "."$quot<p>";
    $quots = (string)$quot;
    $laenge = strlen($quots);
    $aqs = preg_split("//", $quots);
    $pz = (int)$aqs[$laenge];
    $sock = $opus->connect();
    if ($sock < 0) {
        echo ("Error: $ERRMSG\n");
    }
    if ($opus->select_db() < 0) {
        echo ("Error: $ERRMSG\n");
    }
    $res = $opus->query("SELECT source_opus FROM $table WHERE source_opus = $source_opus ");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $urn = $NBN . $pz;
        $stat = $opus->query("UPDATE $table SET urn = '$urn' WHERE source_opus = $source_opus");
        if ($stat < 0) {
            print ("ERROR $table: $ERRMSG <p><HR>\nURN wurde nicht eingetragen!");
        } else {
            print ("Die URN $urn f&uuml;r die $projekt-IDN $source_opus wurde in die Tabelle $table eingetragen.");
            print ("<P>Bitte noch auf den Knopf <B>Indexdatei erstellen</B> dr&uuml;cken (f&uuml;r den Zugang
 &uuml;ber Suchmaschinen)");
            print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/indexfile.$php\">
<INPUT TYPE = submit  VALUE=\"Indexdatei erstellen\">
<INPUT TYPE = hidden NAME = \"von\" VALUE =\"$source_opus\">
<INPUT TYPE = hidden NAME = \"bis\" VALUE =\"$source_opus\">
</FORM>
");
        }
    } else {
        print ("<p>IDN $source_opus nicht vorhanden!");
    }
}
$opus->free_result($res);
$opus->close($sock);
$design->foot("urn_berechnen.$php", $la);
?>



