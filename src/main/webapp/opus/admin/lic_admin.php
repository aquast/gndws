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
 * @version     $Id: lic_admin.php 214 2007-05-17 10:28:15Z freudenberg $
 */
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
$doku_pfad = $opus->value("doku_pfad");
$projekt = $opus->value("projekt");
$lic_active = $opus->value("license_active");
$titel = $opus->value("titel_lic_admin");
$ueberschrift = $opus->value("ueberschrift_lic_admin");
# A.Maile 28.7.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
//$ueberschrift = $opus->value("ueberschrift_neu_abfrage");
# Annette Maile, 20.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
#$opus->opushead("$titel");
//include("../../lib/body.html");
//print ("$ueberschrift");
//include("../../lib/ueberschriftende.html");
print ("<FONT COLOR=red>");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
if ($lic_active > 0) {
    print ("<table class=\"infobox\" width=\"90%\"><tr><td class=\"info\">\n");
    print ("
	Bitte w&auml;hlen Sie die gew&uuml;nschte Lizenz aus.<br>
	Mehr Informationen sowie den g&uuml;ltigen (rechtlich bindenden) Vertrag erhalten Sie mit einem Klick auf die Lizenz.
    ");
    print ("</td></tr>\n</table><p />\n");
    $lic_standard = $opus->value("standard_license");
    $lic_info = $opus->value("license_info");
    $licsql = "SELECT shortname,longname,desc_text,link,pod_allowed,logo,active,sort from license_$la ";
    $admin_active_only = $opus->value("admin_active_only");
    if ($lic_active == 1) {
        $licsql.= " WHERE shortname = '" . $lic_standard . "'";
        if ($admin_active_only) {
            $licsql.= " AND active = 1";
        }
        $licsql.= " ORDER BY sort";
        $licres = $opus->query($licsql);
        $licnum = $opus->num_rows($licres);
        if ($licnum == 1) {
            $licrow = $opus->fetch_row($licres);
            $licname = $licrow[1];
            $licdesc = nl2br(htmlentities($licrow[2]));
            $liclogo = $licrow[5];
            $liclink = "<a href=\"" . $licrow[3] . "\" target=\"_blank\">";
            if ($licrow[4] == 1) {
                $licpod = "Yes";
            } else {
                $licpod = "No";
            }
            if ($licrow[6] == 1) {
                $licact = "Yes";
            } else {
                $licact = "No";
            }
            print ("<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" width=\"90%\">
		<tr>
		<td colspan=\"3\">
		In $projekt ist momentan ausschlie&szlig;lich die folgende Standard-Lizenz verf&uuml;gbar 
		(weitere Lizenzen k&ouml;nnen nur durch " . $projekt . " hinzu gef&uuml;gt werden).
		Die Spalte POD gibt an, ob das Anfertigen einer Print-on-Demand-Kopie gestattet ist.
		</td>
		<td>POD</td>
        <td>Aktiv</td>
		</tr>
	    ");
            $lic_uselogos = $opus->value("license_uselogos");
            if (($lic_uselogos) AND (strlen($liclogo) > 0)) {
                $liclo = "<img src=\"" . $liclogo . "\" alt=\"Logo\" border=\"0\">";
            } else {
                $liclo = "&nbsp;";
            }
            print ("<tr>
		<td valign=\"top\">" . $liclink . $liclo . "</a></td>
		<td valign=\"top\">" . $liclink . $licname . "</a></td>
		<td><small>" . $licdesc . "</small></td>
		<td valign=\"top\">" . $licpod . "</td>
		<td valign=\"top\">" . $licact . "</td>
		</tr>
		</table>
	    ");
        }
    } else {
        if ($admin_active_only) {
            $licsql.= " WHERE active = 1";
        }
        $licsql.= " ORDER BY sort";
        $licres = $opus->query($licsql);
        $licnum = $opus->num_rows($licres);
        if ($licnum > 0) {
            print ("<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" width=\"90%\">
		<tr>
		<td colspan=\"3\">
		In $projekt sind die folgenden Lizenzen verf&uuml;gbar 
		(weitere Lizenzen k&ouml;nnen nur durch " . $projekt . " hinzu gef&uuml;gt werden).
		Die Spalte POD gibt an, ob das Anfertigen einer Print-on-Demand-Kopie gestattet ist.
		</td>
		<td>POD</td>
        <td>Aktiv</td>
		</tr>
	    ");
            $licount = 0;
            while ($licount < $licnum) {
                $licount++;
                $licrow = $opus->fetch_row($licres);
                $licname = $licrow[1];
                $licdesc = nl2br(htmlentities($licrow[2]));
                $liclogo = $licrow[5];
                $liclink = "<a href=\"" . $licrow[3] . "\" target=\"_blank\">";
                if ($licrow[4] == 1) {
                    $licpod = "Yes";
                } else {
                    $licpod = "No";
                }
                if ($licrow[6] == 1) {
                    $licact = "Yes";
                } else {
                    $licact = "No";
                }
                $lic_uselogos = $opus->value("license_uselogos");
                if (($lic_uselogos) AND (strlen($liclogo) > 0)) {
                    $liclo = "<img src=\"" . $liclogo . "\" alt=\"Logo\" border=\"0\">";
                } else {
                    $liclo = "&nbsp;";
                }
                print ("<tr>
		    <td valign=\"top\">" . $liclink . $liclo . "</a></td>
		    <td valign=\"top\">" . $liclink . $licname . "</a></td>
		    <td><small>" . $licdesc . "</small></td>
		    <td valign=\"top\">" . $licpod . "</td>
		    <td valign=\"top\">" . $licact . "</td>
		    </tr>
		");
            }
            print ("</table>\n");
        } else {
            print ("\nKeine (aktiven) Lizenzen vorhanden ?!\n");
        }
    }
} else {
    print ("Keine verfuegbaren Lizenzen (Lizenz-Modul ist nicht aktiviert.)");
}
$design->foot("lic_admin.$php", $la);
?>

