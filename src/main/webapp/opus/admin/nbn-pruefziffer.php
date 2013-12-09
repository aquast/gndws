<html>
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
 * @version     $Id: nbn-pruefziffer.php 235 2007-05-31 05:50:32Z freudenberg $
 */
<head>
<title> NBN-Pruefziffer-Berechnung fuer das Projekt CARMEN-AP4 an Der Deutschen Bibliothek</title>
</head>

<?php
include ("../../lib/stringValidation.php");
$berechnen = $_REQUEST['berechnen'];
if (!_is_valid($berechnen, 0, 50, "[A-Za-z0-9\ ]*")) {
    die("Fehler in Parameter berechnen");
}
$NBN = $_REQUEST['NBN'];
if (!_is_valid($NBN, 0, 300, "[A-Za-z0-9\:\.\_\-]*")) {
    die("Fehler in Parameter NBN");
}
//Programm berechnet Pruefziffer der vollständigen NBN fur das Projekt
//CARMEN-AP4
//an Der Deutschen Bibliothek. Die verbale Beschreibung des Algorithmus zur
//Berechnung der Pruefziffer ist
//aus dem Dokument 'NBNPruefziffer.doc' ersichtlich.
//
//Autor: Kathrin Schroeder
//Stand: 12.07.2001
echo "<body>\n";
?>

<table width=100%>
<tr><td align=left></td>
<td align=right><IMG src="http://nbn-resolving.de/signet_12.gif"></td>
</tr>
</table>

<?php
echo "<p>\n";
#echo "<br>\n";
echo "<h2 align=center style='color:#00008B'> URN-Prüfziffer-Berechnung Der Deutschen Bibliothek</h2>\n";
echo "<table width=75% align=center>\n";
echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"get\">\n";
echo "<tr>\n";
#echo "<td colspan=2 height=150><b style='color:#00008B'>Hinweis:</b> Geben Sie bitte die vollständige NBN, beginnend mit \"urn:nbn:de:...\", an.</td>\n";
echo "<td colspan=2 <b style='color:#00008B'>Hinweis:</b> Geben Sie bitte die vollst\344ndige NBN, beginnend mit \"urn:nbn:de:...\", an.</td>\n";
echo "</tr><tr><td><br></td></tr>\n";
echo "<tr>\n";
echo "<td><b> Geben Sie die NBN ein: </b></td>\n";
echo "<td>\n";
echo "<input type=text size=50 maxlength=200 name='NBN' value='$NBN'>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
#echo "<td cospan=2 height=100>\n";
echo "<td cospan=2>\n";
echo "<input type=submit name='berechnen' value='URN berechnen'>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</form>\n";
echo "</table>\n";
echo "<br>\n";
echo "<br>\n";
if ($berechnen == 'URN berechnen') {
    $NBN = strtolower($NBN);
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
        //echo "Länge der NBN: "."$z<p>";
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
        echo "<table align=center border=2>\n";
        echo "<th colspan=2>Ergebnis</th>\n";
        echo "<tr>\n";
        echo "<td> Die Prüfziffer lautet:</td>\n";
        echo "<td>$pz</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td> Die NBN mit Prüfziffer lautet:</td>\n";
        echo "<td>$NBN" . "$pz</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
    }
}
?>

<p class="iconleiste">

<table border="0" cellpadding="2" cellspacing="1">

<tr height="33">

<td class="iconleistezab" valign="bottom" height="33" width="250">

<a href="mailto:schroeder@dbf.ddb.de">Kathrin Schroeder</a></td>
</tr>

<tr>
<td class="copyright" colspan="5">Copyright: Die Deutsche Bibliothek<br>
Kathrin Schroeder / 14.07.2001
</td>
</tr>
</table>
</p>

</body>
</html>


