<?php
/**
 * Erstellung einer statischen Indexdatei fuer Suchmaschinen
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
 * @author      Annette Maile <maile@ub.uni-stuttgart.de>
 * @author 	    Oliver Marahrens <o.marahrens@tu-harburg.de>
 * @author      Stefan Freudenberg <stefan.freudenberg@bsz-bw.de>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id: indexfile.php 321 2007-09-13 12:55:46Z marahrens $
 */
#############################################################
#
# Funktion: admin/indexfile.php
#           Erstellung statische Indexdatei fuer Suchmaschinen
# letzte Aenderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
#
# 29.7.2005, Annette Maile, UB Stuttgart
# Einfuegen Lizenzvertrag
#
# 03.04.2007, Oliver Marahrens, TUB Hamburg-Harburg
# Connotea-Schnittstellenintegration
#
#############################################################

require '../../lib/opus.class.php';
require '../../lib/stringValidation.php';

$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value('php');
$table = $opus->value('opus_table');
$url = $opus->value('url');
$projekt = $opus->value('projekt');
$home = $opus->value('home');
$titel = $opus->value('titel_indexfile');
$ueberschrift = $opus->value('ueberschrift_indexfile');

$von = $_REQUEST['von'];
if (!_is_valid($von, 0, 10, "[0-9]*")) {
    die('Fehler in Parameter von');
}
$bis = $_REQUEST['bis'];
if (!_is_valid($bis, 0, 10, "[0-9]*")) {
    die('Fehler in Parameter bis');
}
# A.Maile 10.2.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value('la');
}
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require "../../lib/design.$php";
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
$a = $von;
$bis = $bis;
while ($a <= $bis) {
    $query = "SELECT source_opus, date_creation, bereich_id, verification " 
		   . "FROM $table WHERE source_opus = $a"; 
    $res = $opus->query($query);
    $num = $opus->num_rows($res);
    if ($num != 0) {
        $mrow = $opus->fetch_row($res);
        $source_opus = $mrow[0];
        $date_creation = $mrow[1];
        $bereich_id = $mrow[2];
        $verification = $mrow[3];
        $opus->free_result($res);
        $jahr = date('Y', $date_creation);
        # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
        # Falls in Altbestaenden (< Opus 3.0) kein Bereich angegeben ist,
        # wird bereich_id auf 1 gesetzt = freier Zugriff auf die Dokumente,
        # sonst kommt eine Fehlermeldung der Datenbank
        if ($bereich_id == "" || $bereich_id == 0) {
            $bereich_id = 1;
        }
        $res = $opus->query("SELECT bereich, volltext_pfad, volltext_url FROM bereich_$la WHERE bereich_id = $bereich_id");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $bereich = $mrow[0];
            $volltext_pfad = $mrow[1];
            $volltext_url = $mrow[2];
            $opus->free_result($res);
        }
        # Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
        if (file_exists("$volltext_pfad/$jahr/$source_opus")) {
            $fd = fopen("$volltext_pfad/$jahr/$source_opus/index.html", 'w');
            if ($fd == 0) {
                echo ("Error: $ERRMSG <BR>\n");
                exit;
            } else {
                $frontdoor = fopen("$url/frontdoor.php?source_opus=$source_opus&la=de", 'r');
                if (!$frontdoor) {
                    echo "<p>Datei $url/frontdoor.php?source_opus=$source_opus&la=de konnte nicht geoeffnet werden.\n";
                    exit;
                }
                while (!feof($frontdoor)) {
                    $line = fgets($frontdoor, 1024);
                    if ($line == "<form method=\"post\" action=\"\">\n") {
                        $line = "<form method=\"post\" action=\"" . $url . "/frontdoor.php\">\n";
                    }
                    fwrite($fd, "$line");
                }
                fclose($fd);
                print ("Indexdatei zu Dokument $source_opus wurde in die Datei <a href=\"$volltext_url/$jahr/$source_opus/index.html\">index.html</a> geschrieben.<P> \n");
                
                $mailPattern = '/^[\w.+-]{2,64}\@[\w.-]{2,255}\.[a-z]{2,6}$/';
                if (preg_match($mailPattern, $verification)) {
                    print ("<hr>\n");
                    print ('<form action="mail_autor.php" method="post"><p>');
                    print ('<input type="hidden" name="source_opus" ' .
                           "value=\"$source_opus\"/>");
                    print ('<input type="hidden" name="verification" ' .
                           "value=\"$verification\"/>");
                    print ('<button type="submit" name="senden">' . 
                           'Autor informieren</button> Den Autor &uuml;ber ' .
                           'die Freigabe per E-Mail informieren.</p>');
                    print ('</form>');
                }
                
                # O.Marahrens 12.11.06: Connotea-Schnittstelle
                $connotea_export = $opus->value('connotea_export');
                if ($connotea_export == 1) {
                    include_once '../../lib/class.connotea.php';
                    # O. Marahrens: Automatisch alle Texte aus der Textdatei holen
                    $connotea_texte = new OPUS("../../texte/$la/connotea.conf");
                    foreach($connotea_texte->getValues() as $k => $v) {
                        $$k = $v;
                    }
                    print ("<HR>\n");
                    include ("../../lib/font.html");
                    print ("<form action=\"connotea_einfuegen.php?la=" . $la . "\" method=\"post\">\n");
                    print ("<input type=\"hidden\" name=\"uri\" value=\"$volltext_url/$jahr/$source_opus/\"/><br/>\n");
                    // Sonst: Bookmarking-Formular
                    $system_tags = $opus->value('system_tags');
                    print ("<input type=\"hidden\" name=\"system_tags\" value=\"" . $system_tags . "\"/><br/>\n");
                    #print("<input type=\"hidden\" name=\"redirect\" value=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\"/><br/>\n");
                    print ("Eigene Tags: <input type=\"text\" name=\"user_tags\" /><br/>\n");
                    print ("Titel: <input type=\"text\" name=\"usertitle\" value=\"" . $title . "\" /><br/>\n");
                    print ("Beschreibung: <input type=\"text\" name=\"userdescription\" value=\"" . $description . "\" /><br/>\n");
                    print ("<input type=\"submit\" name=\"connotea_bm\" value=\"$connotea_bookmark\"/>\n");
                    print ("</form>\n<br/><hr/><br/>");
                }
                // Ende der Connotea-Schnittstelle
                
            }
        } else {
            print ("$volltext_pfad/$jahr/$source_opus nicht vorhanden.<BR> \n");
        }
    } else {
        print ("$projekt-IDN $a nicht vorhanden.<BR> \n");
    }
    $a++;
}
print ("<P><A HREF=\"$home/admin/\">Adminseite</A>");
$opus->close($sock);
$design->foot("indexfile.$php", $la);
?>

