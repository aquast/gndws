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
 * @version     $Id: dateimanager.php 300 2007-09-04 17:48:57Z becker $
 */
/*
File: dateimanager.php  Rev. 20.12.2004
------------------------------------
For use with OPUS Document Server by A. Maile, UB Stuttgart
Based on 'change_document_files.php' by G. Schupfner, UB Regensburg
Modified by M. Reymann, UB Regensburg
*/
#############################################################
# letzte Aenderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# 03.05.2007, Oliver Marahrens, TUB Hamburg-Harburg
# Checksummen-Erweiterung:
# Anzeige der Checksummen fuer jede Datei und automatische Validierung
# Gelegenheit zum Neuberechnen der Checksummen
#
# 04.09.2007, Pascal-Nicolas Becker, KOBV Berlin-Brandenburg
# Dateimanager kann nun auch Dokumente der Opus-Tabelle bearbeiten.
#
#############################################################
require '../../lib/opus.class.php';
require '../../lib/class.checksum.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$temp_table = $opus->value("temp_table");
$opus_table = $opus->value("opus_table");
$url = $opus->value("url");
$mod_checksum = $opus->value("mod_checksum");
$mod_gpg = $opus->value("mod_gpg");

$PHP_SELF = $_SERVER["PHP_SELF"];

$db = $opus->value("db");
$bemerkung_datei_admin = $opus->value("bemerkung_datei_admin");
$titel = $opus->value("titel_dateimanager");
$ueberschrift = $opus->value("ueberschrift_dateimanager");
include ("../../lib/stringValidation.php");
$userfile = $_FILES['userfile'];
$anz_files = count($userfile['name']);
$bem_userfile = $_REQUEST['bem_userfile'];
$bem_userfile = _zeichen_loeschen("[`\n\r]", "", $bem_userfile);
$creator_name = $_REQUEST['creator_name'];
$creator_name = _zeichen_loeschen("[`\n\r]", "", $creator_name);
$title = $_REQUEST['title'];
$title = _zeichen_loeschen("[`\n\r]", "", $title);
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus: $source_opus");
}
# Ueberpruefe Tabelle und bestimme Pfad der Dokumente und entsprechende URL
$table = $_REQUEST['table'];
if ($table == $temp_table) {
    $texte_dir = $opus->value("incoming_pfad");
    $texte_url = $opus->value("incoming_url");
} elseif ($table == $opus_table) {
    if ($opus->value("veroeffentlichte_dateien_aendern") != 1) {
        die("Dateien der Opus-Tabelle k&ouml;nnen nur geändert werden, wenn veroeffentlichte_dateien_aendern in opus.conf auf 1 gesetzt ist.");
    }
    
    $sock = $opus->connect();
    if ($sock < 0) {
        print ("<FONT COLOR=red>");
        echo ("Error: $ERRMSG\n");
        print ("</FONT>");
    }
    if ($opus->select_db() < 0) {
        print ("<FONT COLOR=red>");
        echo ("Error: $ERRMSG\n");
        print ("</FONT>");
    }
    $res = $opus->query("SELECT volltext_pfad, volltext_url FROM bereich_$la WHERE bereich_id = (SELECT bereich_id FROM $opus_table WHERE source_opus = $source_opus)");
    $num = $opus->num_rows($res);
    if ($num == 0) {
        die("Kann Bereich für Dokument mit source_opus $source_opus aus Tabelle $table nicht ermitteln.");
    }
    $mrow = $opus->fetch_row($res);

    $texte_dir = $mrow[0];
    $texte_url = $mrow[1];
    
    $opus->free_result($res);
    $opus->close($sock);
} else {
    die("Fehler in Parameter table: $table");
}
# ENDE Tabellenueberpruefung, Pfad- und URL-Bestimmung
$jahr = $_REQUEST['jahr'];
if (!_is_valid($jahr, 4, 4, "[0-9]+")) {
    die("Fehler in Parameter jahr");
}
$dir_format = $_REQUEST['dir_format'];
if (!_is_valid($dir_format, 0, 10, "[A-Za-z]*")) {
    die("Fehler in Parameter dir_format");
}
$pathname = $_REQUEST['pathname'];
if (!_is_valid($pathname, 0, 300, "[A-Za-z0-9\/\.\_\-]*")) {
    die("Fehler in Parameter pathname");
}
$bem_pathname = $_REQUEST['bem_pathname'];
if (!_is_valid($bem_pathname, 0, 300, "[A-Za-z0-9\/\.\_\-]*")) {
    die("Fehler in Parameter bem_pathname");
}
$bem_file = $_REQUEST['bem_file'];
$bem_file = _zeichen_loeschen("[`\n\r]", "", $bem_file);
$action = $_REQUEST['action'];
if (!_is_valid($action, 0, 20, "[a-z\_]*")) {
    die("Fehler in Parameter action");
}
if ($_REQUEST["delete"])
{
	$action = "delete";
}
if ($_REQUEST["checksums"])
{
	$action = "checksums";
}
if ($_REQUEST["make_sig"])
{
	$action = "bibl_sign"; 
}

# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
function getSubdirs($path) {
    $subdirs = array();
    $dir = opendir($path);
    while (false != ($entry = readdir($dir))) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        if (is_dir($path . "/" . $entry)) {
            $subdirs[] = $entry;
        }
    }
    closedir($dir);
    return $subdirs;
}
function getFiles($path) {
    $files = array();
    $dir = opendir($path);
    while (false != ($entry = readdir($dir))) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        if (is_file($path . "/" . $entry)) {
            $files[] = $entry;
        }
    }
    closedir($dir);
    sort($files);
    return $files;
}
function getDirList($path, $level = 0) {
    global $PHP_SELF, $creator_name, $title, $source_opus, $jahr, $bemerkung_datei_admin, $la, $mod_checksum, $mod_gpg, $texte_dir, $texte_url, $table;
    $dir = opendir($path);
    $nentries = 0;
    $spaces = '';
    for ($i = 0;$i < $level;$i++) {
        $spaces.= '&nbsp;&nbsp;';
    }
    while (false != ($entry = readdir($dir))) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        $entries[] = $entry;
        $nentries++;
        //    sort($entries);
        
    }
    for ($i = 0;$i < $nentries;$i++) {
        if (is_dir($path . "/" . $entries[$i])) {
            echo ("<form action=\"$PHP_SELF\" method=\"POST\" enctype=\"multipart/form-data\">\n");
            echo ("$spaces$entries[$i]:\n");
            echo ("<input type=\"file\" name=\"userfile\">\n");
            if ($bemerkung_datei_admin) {
                echo ("Bemerkung: <input type=\"text\" name=\"bem_userfile\">\n");
            }
            echo ("<INPUT TYPE = submit  VALUE=\"Neue $entries[$i] - Datei hochladen\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"creator_name\" VALUE=\"$creator_name\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"jahr\" VALUE=\"$jahr\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"pathname\" VALUE=\"$path/$entries[$i]\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"action\" VALUE=\"upload\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"table\" VALUE=\"$table\">\n");
            echo ("<input type=\"hidden\" name=\"la\" value=\"$la\">\n");
            echo ("<br><br></form>\n");
            getDirList($path . "/" . $entries[$i], $level+1);
        } else {
            $urlpath = ereg_replace($texte_dir, $texte_url, $path);
            $entry_enc = urlencode($entries[$i]);
            $entry_enc = htmlentities($entry_enc);
            $entry_enc = ereg_replace("\+", "%20", $entry_enc);
	        $path_enc = urlencode($path);
    	    $path_enc = htmlentities($path_enc);
        	$path_enc = ereg_replace("\+","%20",$path_enc);
            /*      if ($level==0 && $entries[$i] == 'index.html') {
            echo("<a href=\"$urlpath/$entry_enc\">$spaces$entries[$i]</a>\n");
            }
            */
            /* Dateien, die mit .bem_ beginnen sind Bemerkungen zu den Dateien,    */
            /* daher werden nur Dateien, die nicht mit .bem_ anfangen aufgelistet, */
            /* damit sie gegebenenfalls geloescht werden koennen.                  */
            if ($level != 0 && $entries[$i] != 'index.html' && ereg("^.bem_*", $entries[$i]) != 1) {
                echo ("<form action=\"$PHP_SELF\" method=\"POST\">\n");
                echo ("<a href=\"$urlpath/$entry_enc\">$spaces$entries[$i]</a>\n");
                // ===============   Checksummen-Erweiterung    ================
                if ($mod_checksum == 1) {
	                echo ("$spaces<INPUT TYPE=\"submit\" name=\"checksums\"  VALUE=\"Pr&uuml;fsummen neu berechnen\">\n");
         			$dirname_array = split("/", $path);
        			$dirname = $dirname_array[(count($dirname_array)-1)];
                	echo ("<INPUT TYPE = hidden NAME=\"entry\" VALUE=\"".$dirname."/".$entries[$i]."\">\n");
                }
                // =============== Ende Checksummen-Erweiterung ================
                echo ("$spaces<INPUT TYPE=\"submit\" name=\"delete\"  VALUE=\"$entries[$i] l&ouml;schen\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"creator_name\" VALUE=\"$creator_name\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"jahr\" VALUE=\"$jahr\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"pathname\" VALUE=\"$path/$entries[$i]\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"bem_pathname\" VALUE=\"$path/.bem_$entries[$i]\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"table\" VALUE=\"$table\">\n");
                echo ("<input type=\"hidden\" name=\"la\" value=\"$la\">\n");
                echo ("</form><br>\n");
                // ===============   Checksummen-Erweiterung    ================
                if ($mod_checksum == 1) {
					if ($table == $opus_table)  {
					    $cs = new Checksum($source_opus, $dirname."/".$entries[$i], "", $la, "opus");
                                        } else {
					    $cs = new Checksum($source_opus, $dirname."/".$entries[$i], "", $la, "temp");
                                        }

					$algos = $cs->listAvailableAlgorithms();
					foreach ($algos as $algo)
					{
						$cs->algorithm = $algo;
						$verification = $cs->verify();
						switch ($verification)
						{
							case 0:
								// Prüfsumme geändert
								echo "<div style=\"color:red;\">Pr&uuml;fsumme ".$cs->algorithm." ist: ".$cs->getChecksum() .
									"Sie sollte sein: ".$cs->getOpusChecksum()."</div>";
								break;
							case 1:
								echo "<div style=\"color:green;\">Pr&uuml;fsumme ".$cs->algorithm." ist: ".$cs->getChecksum()."</div>";
								break;
							default:
								echo "<div>Nicht unterst&uuml;tzter Algorithmus ".$cs->algorithm."...</div>";
								break;
						}
					}
                }
                // =============== Ende Checksummen-Erweiterung ================ 
                // =================== Signatur-Erweiterung ==================== 
                if ($mod_gpg == 1 && GPG::isInstalled() === true)
                {
                	$s = new Signature($path."/".$entries[$i]);
                	$sig = $s->authorVerify();
                	switch ($sig) {
                    	case 0:
	                        echo "<br/><span style=\"color:red;\">Autorensignatur ung&uuml;ltig!</span>";
                        		break;
                    	case 1:
	                        echo "<br/><span style=\"color:green\">Autorensignatur g&uuml;ltig!</span>";
                        	break;
                    	case 2:
	                        echo "<br/>Der Autor hat diese Datei nicht signiert.";
                        	break;
                    	case 3:
	                        echo "<br/><span style=\"color:red\">Fehler bei der &Uuml;berpr&uuml;fung der Autorensignatur; evtl. korrupte Signaturdatei!</span>";
                        	break;
                	}
					if (($sig == 1 || $sig == 0) && $s->getGpgMessage()) {
    					echo "<br/>".htmlspecialchars($s->getGpgMessage());
					}

                	$biblsig = $s->biblVerify();
                	switch ($biblsig) {
                    	case 0:
                        	echo "<br/><span style=\"color:red;\">Bibliothekssignatur ung&uuml;ltig!</span>";
                        	break;
                    	case 1:
	                        echo "<br/><span style=\"color:green\">Bibliothekssignatur g&uuml;ltig!</span>";
    	                    break;
        	            case 2:
            	                echo "<br/><span style=\"color:red;\">Keine Bibliothekssignatur vorhanden!</span>";
			        echo "<form action=\"$PHP_SELF\" method=\"POST\">\n";
                		echo "<input type=\"hidden\" name=\"la\" value=\"$la\">\n";
				echo "<input type=\"hidden\" name=\"source_opus\" value=\"$source_opus\">\n";
				echo "<input type=\"hidden\" name=\"entry\" value=\"$entry_enc\">\n";
				echo "<input type=\"hidden\" name=\"path\" value=\"$path_enc\">\n";
				echo "<input type=\"hidden\" name=\"jahr\" value=\"$jahr\">\n";
				echo "<input type=\"hidden\" name=\"title\" value=\"$title\">\n";
				echo "<input type=\"hidden\" name=\"pathname\" value=\"$path/$entries[$i]\">\n";
				echo "<input type=\"hidden\" name=\"bem_pathname\" value=\"$path/.bem_$entries[$i]\">\n";
                                echo "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
	        		if (!$s->gpg_pass) echo "Schl&uuml;sselpasswort: <input type=\"password\" name=\"sig_passwd\" />";
    	    			echo "<input type=\"submit\" name=\"make_sig\" value=\"Signieren\" />";
                		echo ("</form><br>\n");
                        	break;
                    	case 3:
	                        echo "<br/><span style=\"color:red;\">Fehler bei der &Uuml;berr&uuml;fung der Bibliothekssignatur; evtl. korrupte Signaturdatei!</span>";
    	                    break;
        	        }
					if (($biblsig == 1 || $biblsig == 0) && $s->getGpgMessage()) {
    					echo "<br/>".htmlspecialchars($s->getGpgMessage());
					}
                }
		        // =============== Ende Signatur-Erweiterung ================
                if ($bemerkung_datei_admin) {
                    $bem_file = "";
                    if (file_exists("$path/.bem_$entries[$i]")) {
                        $fd = fopen("$path/.bem_$entries[$i]", "r");
                        $bem_file = fread($fd, filesize("$path/.bem_$entries[$i]"));
                        fclose($fd);
                    }
                    echo ("<form action=\"$PHP_SELF\" method=\"POST\">\n");
                    echo ("<br>Bemerkung zu $entries[$i]: \n");
                    echo ("<INPUT TYPE = \"text\" NAME=\"bem_file\" VALUE=\"$bem_file\" size=\"30\">\n");
                    echo ("<INPUT TYPE = hidden NAME=\"bem_pathname\" VALUE=\"$path/.bem_$entries[$i]\">\n");
                    echo ("<INPUT TYPE = \"hidden\" NAME=\"action\" VALUE=\"bem_aendern\">\n");
                    echo ("$spaces<INPUT TYPE = submit  VALUE=\"Bemerkung zu $entries[$i] &auml;ndern\">\n");
                    echo ("<INPUT TYPE = hidden NAME=\"creator_name\" VALUE=\"$creator_name\">\n");
                    echo ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\">\n");
                    echo ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\">\n");
                    echo ("<INPUT TYPE = hidden NAME=\"jahr\" VALUE=\"$jahr\">\n");
		    echo "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
                    echo ("<input type=\"hidden\" name=\"la\" value=\"$la\">\n");
                    echo ("</form><p>\n");
                }
            }
            echo ("\n");
        }
    }
    closedir($dir);
    if ($level > 0) {
        echo ("<hr width=\"50%\" align=\"left\"");
    }
}
function getFormats() {
    global $opus, $db;
    $res = array();
    print ("<FONT COLOR=red>");
    $sock = $opus->connect();
    if ($sock < 0) {
        echo ("Error: $ERRMSG\n");
    }
    if ($opus->select_db() < 0) {
        echo ("Error: $ERRMSG\n");
    }
    print ("</FONT>");
    $res = $opus->query("SELECT name, diss_format, extension FROM format");
    $num = $opus->num_rows($res);
    if ($num == 0) {
        echo ("Tabelle \"format\" in \"" . $opus . "\" ist leer!");
        return (FALSE);
    }
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow[] = $opus->fetch_row($res);
    }
    return ($mrow);
}
function isValidFormat($format) {
    $validformats = getFormats();
    $valid = 0;
    foreach($validformats as $vformat) {
        if ($format == $vformat[2]) $valid = 1;
    }
    if ($valid == 0) {
        echo ("\"$format\" ist kein g&uuml;ltiges Format!<br>");
        return (FALSE);
    }
    return (TRUE);
}
// SICHERHEIT: Teste Gültigkeit der geposteten Argumente ($jahr 1000-2999, $source_opus > 0)
if (!preg_match("/^[1-9]\d*$/", $source_opus) || !preg_match("/^[12]\d{3}$/", $jahr)) {
    echo ("Invalid Arguments. Exiting.");
    exit();
}
// Switch ACTION:
$title = htmlspecialchars(stripslashes($title));
switch ($action) {
  // Datei signieren
  case 'bibl_sign':
        if ($mod_gpg)
        {
        	$entry = ereg_replace("%20","\+",$_REQUEST["entry"]);
        	$entry = html_entity_decode($entry);
        	$entry = urldecode($entry);
        	$path = ereg_replace("%20","\+",$_REQUEST["path"]);
        	$path = html_entity_decode($path);
        	$path = urldecode($path);
        	$pathname = $path."/".$entry;
			$s = new Signature($pathname);
			if ($_POST["sig_passwd"]) 
			{
				$s->signFile($_POST["sig_passwd"]);
			}
			else
			{
				$s->signFile();
			}
        }
        else
        {
        	echo("<span style=\"color:red\">Die Signatur-Funktionen Ihrer OPUS-Installation sind deaktiviert! Daher steht der Parameter 'bibl_sign' nicht zur Verf&uuml;gung!</span><hr>\n");
        }
  break;
  // Checksumme neu berechnen nach Dateiaustausch
  case 'checksums':
		if ($mod_checksum) 
		{
			$entry = ereg_replace("%20","\+",$_REQUEST["entry"]);
			$entry = html_entity_decode($entry);
			$entry = urldecode($entry);
                        if ($table == $opus_table) {
                            $cs = new Checksum($source_opus, $entry, "", $la, "opus");
                        } else {
			    $cs = new Checksum($source_opus, $entry, "", $la, "temp");
                        }
			$cs->registerChecksums();
			echo("<span style=\"color:green\">Die Pr&uuml;fsumme(n) der Datei '".$cs->file_path."' wurde(n) neu berechnet!</span><hr>\n");
		}
		else {
			echo("<span style=\"color:red\">Die Pr&uuml;fsummen-Funktionen Ihrer OPUS-Installation sind deaktiviert! Daher steht der Parameter 'change_checks
um' nicht zur Verf&uuml;gung!</span><hr>\n");
		}
  break;

        // alte Dateien loeschen
        
    case 'delete':
        echo ("<font color=\"red\">Datei '$pathname' wurde gel&ouml;scht. Bitte Indexdatei neu erzeugen.</font><hr>\n");
        unlink($pathname);
        if (file_exists($bem_pathname)) {
            unlink($bem_pathname);
        }
    break;
    case 'bem_aendern':
        if ($bem_file == "") {
            unlink($bem_pathname);
        } else {
            $fd = fopen("$bem_pathname", "w");
            fwrite($fd, "$bem_file");
            fclose($fd);
        }
    break;
        // neue Dateien hochladen
        
    case 'upload':
        if ($anz_files > 0) {
            $dateiname = urldecode($userfile['name']);
            $dateiname = $opus->reduce_iso($dateiname);
            if (file_exists("$pathname/$dateiname")) {
                echo "<font color=\"red\">Datei '$dateiname' existiert bereits. Wenn Sie die Datei ersetzen
m&ouml;chten, bitte zuerst die Datei l&ouml;schen und dann neu hochladen.</font><hr>\n";
            } else {
                move_uploaded_file($userfile['tmp_name'], "$pathname/$dateiname");
                if ($bem_userfile != "") {
                    $fd = fopen("$pathname/.bem_$dateiname", "w");
                    fwrite($fd, "$bem_userfile");
                    fclose($fd);
                }
                echo ("<font color=\"red\">Datei '$dateiname' wurde hochgeladen. Bitte Indexdatei neu erzeugen.</font>\n");
            }
            echo ("<hr>\n");
        } else {
            echo "<font color=\"red\">Hochladen der Datei fehlgeschlagen.</font><hr>\n";
        }
    break;
        // neue Temp-Indexdatei
        // Indexdateien fuer Dokumente der Opus-Tabelle werden mittels indexfile.php erzeugt.
        
    case 'create_index':
        echo ("<font color=\"red\">Indexdatei wurde aktualisiert.</font><hr>\n");
        $ind = fopen("$texte_dir/$jahr/$source_opus/index.html", 'w');
        fwrite($ind, "<HTML>\n<HEAD></HEAD>\n<BODY BGCOLOR=FFFFFF ALINK=FFFF00 VLINK=FF0000>\n");
        fwrite($ind, "<FONT SIZE=-1><A HREF=\"../doku/urheberrecht.html\">Hinweis zum Urheberrecht</A></FONT>\n<HR>\n");
        fwrite($ind, "<P>\n<B>$creator_name</B>\n<H3>$title</H3>\n\n<HR>\n\n");
        fwrite($ind, "<P>Dieses Dokument liegt in folgenden Teildokumenten vor: <P>\n");
        $subdirs = getSubdirs("$texte_dir/$jahr/$source_opus");
        for ($i = 0;$i < count($subdirs);$i++) {
            $urlpath = "$texte_url/$jahr/$source_opus/$subdirs[$i]";
            fwrite($ind, "<CENTER>\n<TABLE BORDER=1>\n<TR>\n");
            $k = 1;
            $files = getFiles("$texte_dir/$jahr/$source_opus/$subdirs[$i]");
            for ($j = 0;$j < count($files);$j++) {
                if ($k > 3) {
                    fwrite($ind, "</TR>\n<TR>\n");
                    $k = 1;
                }
                $ext = substr(strrchr($files[$j], "."), 1);
                $entry_enc = urlencode($files[$j]);
                $entry_enc = htmlentities($entry_enc);
                $entry_enc = ereg_replace("\+", "%20", $entry_enc);
                $docnum = $j+1;
                /* Dateien, die mit .bem_ beginnen sind Bemerkungen zu den Dateien,    */
                /* daher werden nur Dateien, die nicht mit .bem_ anfangen aufgelistet, */
                /* Falls die Bermerkungsdatei vorhanden ist, wird ihr Inhalt ausgegeben*/
                if (ereg("^.bem_*", $files[$j]) != 1) {
                    fwrite($ind, "<TD><A HREF=\"$urlpath/$entry_enc\">$files[$j]</A>&nbsp;&nbsp;&nbsp;\n");
                    $bem_datei = "$texte_dir/$jahr/$source_opus/$subdirs[$i]/.bem_$files[$j]";
                    if (file_exists($bem_datei)) {
                        $fd = fopen("$bem_datei", "r");
                        $bemerkung = fread($fd, filesize("$bem_datei"));
                        fclose($fd);
                        fwrite($ind, "$bemerkung\n");
                    }
                    /* A. Maile, 15.6.05, einfuegen von pdfinfo, falls in opus.conf gesetzt */
                    $use_pdfinfo = $opus->value("pdfinfo_active");
                    if ($use_pdfinfo) {
                        $datei = "$texte_dir/$jahr/$source_opus/$subdirs[$i]/$files[$j]";
                        $pdfinfopath = $opus->value("pdfinfo_path");
                        $pdfinfo_inc = $opus->value("pdfinfo_inc");
                        include $pdfinfo_inc;
                    }
                }
                fwrite($ind, "</TD>\n");
                $k++;
            }
            fwrite($ind, "\n</TR>\n</TABLE> \n");
        }
        fwrite($ind, "<HR>\n<CENTER><A HREF=\"../admin/\">Home</A>\n<CENTER>\n</BODY>\n</HTML>\n");
        fclose($ind);
    break;
        // erzeuge ordner
        
    case 'create_directory':
        echo ("<font color=red>");
        if (isValidFormat($dir_format) && !is_dir("$texte_dir/$jahr/$source_opus/$dir_format")) {
            echo ("<b>'$dir_format' - Ordner wurde erzeugt.</b><br><br>");
            //echo("mkdir (\"$texte_dir/$jahr/$source_opus/$dir_format\", 0755) <br><br>\n");
            mkdir("$texte_dir/$jahr/$source_opus/$dir_format", 0755);
        } else echo ("Der \"$dir_format\" Ordner existiert bereits.<br><br>");
        echo ("</font>");
        break;
        // loesche ordner (wenn leer - sonst warnen)
        
    case 'delete_directory':
        echo ("<font color=red>");
        if (isValidFormat($dir_format) && is_dir("$texte_dir/$jahr/$source_opus/$dir_format") && count(getFiles("$texte_dir/$jahr/$source_opus/$dir_format")) == 0) {
            echo ("<b>'$dir_format' - Ordner wurde gel&ouml;scht.</b><br><br>");
            //echo("rmdir $texte_dir/$jahr/$source_opus/$dir_format<br><br>\n");
            rmdir("$texte_dir/$jahr/$source_opus/$dir_format");
        } else echo ("Der \"$dir_format\" Ordner ist nicht leer! L&ouml;schen Sie bitte zuerst alle Dateien in diesem Ordner.<br><br>");
        echo ("</font>");
        break;
    }
    // Dokumenttitel, Autor anzeigen
    echo ("<b>$creator_name</b>\n<h3>$title</h3><hr>\n");
    // Aufforderung, Ordner anzulegen, falls keine vorhanden
    if ((is_dir("$texte_dir/$jahr/$source_opus")) && (count(getSubdirs("$texte_dir/$jahr/$source_opus")) == 0)) {
        echo ("F&uuml;r dieses Dokument (ID: $source_opus) existieren keine Unterordner.<br>Bitte w&auml;hlen Sie die Formate aus, f&uuml;r die Ordner erzeugt werden sollen.<br><br>\n");
    }
    // Dateidialog nur anzeigen, wenn Unterverz. existieren: ---------------------+
    if ((is_dir("$texte_dir/$jahr/$source_opus")) && (count(getSubdirs("$texte_dir/$jahr/$source_opus")) > 0)) {
        // Link & Button fuer Indexdatei
        // Fuer Dokumente der Opus-Tabelle wird die Indexdatei mittels index.php erzeugt, fuer Dokumente der Temp-Tabelle mittels des Dateimanagers.
        if($table == $opus_table) {
            echo("<br><FORM METHOD=\"POST\" ACTION=\"$url/admin/indexfile.$php\"> \n");
            if (file_exists("$texte_dir/$jahr/$source_opus/index.html")) {
                echo ("<a href=\"$texte_url/$jahr/$source_opus/index.html\">index.html</a> ");
            }
            echo("<INPUT TYPE=\"submit\"  VALUE=\"Neue Indexdatei erzeugen\"> \n");
            echo("<INPUT TYPE=\"hidden\" NAME=\"von\" VALUE =\"$source_opus\"> \n");
            echo("<INPUT TYPE=\"hidden\" NAME=\"bis\" VALUE =\"$source_opus\"> \n");
            echo("</FORM><br><br> \n");
        } else {
            echo ("<br><form action=\"$PHP_SELF\" method=\"POST\">\n");
            if (file_exists("$texte_dir/$jahr/$source_opus/index.html")) {
                echo ("<a href=\"$texte_url/$jahr/$source_opus/index.html\">index.html</a> ");
            }
            echo ("<INPUT TYPE = submit  VALUE=\"Neue Indexdatei erzeugen\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"creator_name\" VALUE=\"$creator_name\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"jahr\" VALUE=\"$jahr\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"action\" VALUE=\"create_index\">\n");
            echo "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
            echo ("<input type=\"hidden\" name=\"la\" value=\"$la\">\n");
            echo ("</form><br><br>\n");
        }
        // Link zurueck zu den Metadaten und der Adminseite
        echo ("<a href=\"aendern.php?suchfeld=source_opus&suchwert=$source_opus&table=$table\">Zur&uuml;ck zum aktualisierten Datensatz</a><br><br>\n");
        echo ("<a href=\"index.php\">Zur&uuml;ck zur Adminseite</a><br><br>\n");
        // Dateien des Dokuments
        echo ("<hr><b><i>Dateien:</i></b><hr>");
        getDirList("$texte_dir/$jahr/$source_opus");
    } // -------------------------------------------------------------------------+
    // Ordner des Dokuments (immer anzeigen)
    echo ("<hr><b><i>Ordner:</i></b><hr>");
    echo ("<table border=1>\n");
    if ($res = getFormats()) {
        foreach($res as $format) {
            echo ("<tr><td><form action=\"$PHP_SELF\" method=\"POST\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"jahr\" VALUE=\"$jahr\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"creator_name\" VALUE=\"$creator_name\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\">\n");
            echo ("<INPUT TYPE = hidden NAME=\"dir_format\" VALUE=\"$format[2]\">\n");
	    echo ("<input type=\"hidden\" name=\"table\" value=\"$table\">\n");
            if ($format[1] == 1) echo ("<u>");
            if (is_dir("$texte_dir/$jahr/$source_opus/$format[2]")) {
                echo ("<b>" . $format[0] . "</b>");
                if ($format[1] == 1) echo ("</u>");
                echo ("</td><td>");
                echo ("<INPUT TYPE = submit VALUE=\"$format[2] Ordner l&ouml;schen\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"action\" VALUE=\"delete_directory\">\n");
            } else {
                echo ("<i>" . $format[0] . "</i>");
                if ($format[1] == 1) echo ("</u>");
                echo ("</td><td>");
                echo ("<INPUT TYPE = submit VALUE=\"$format[2] Ordner erzeugen\">\n");
                echo ("<INPUT TYPE = hidden NAME=\"action\" VALUE=\"create_directory\">\n");
            }
            echo ("<input type=\"hidden\" name=\"la\" value=\"$la\">\n");
            echo ("</form></td></tr> \n");
        }
    } else {
        echo ("Formattabelle leer!");
    }
    echo ("</table>\n");
    echo ("<br><br> (<b>Fett:</b> Ordner existiert - <i>Kursiv:</i> Ordner existiert nicht - <u>Unterstrichen:</u> Pflichtformat)");
    // Ende Ordner
    $design->foot("dateimanager.$php", $la);
?>
