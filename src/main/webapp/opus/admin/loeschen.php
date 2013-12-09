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
 * @version     $Id: loeschen.php 214 2007-05-17 10:28:15Z freudenberg $
 */
#############################################################
#
# Funktion: admin/db_aendern.php
#           Abspeichern der Metadaten in der Datenbank
# letzte Änderung:
#
# 20.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
include ("../../lib/class.mime_mail.$php");
$mail = new mime_mail();
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$incoming_pfad = $opus->value("incoming_pfad");
$projekt = $opus->value("projekt");
$email = $opus->value("email");
$opusmail = $opus->value("opusmail");
$home = $opus->value("home");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
// Änderung für URN-Verwaltung
//
// Folgende Zeilen wurden in Opus-Conf reingeschrieben
//
//	/* Email-Adresse für X-Epicur-Meldungen
//	xepicur_email = urn-transaction@nbn-resolving.org
//
//	/* URN_SNID fuer die Vergabe von URN bei der Deutschen Bibliothek */
//	/* z.B. UB Stuttgart: nbn = urn:nbn:de:bsz:93 */
//	urn_snid = urn:nbn:de:hbz:nn
$xepicur_email = $opus->value("xepicur_email");
$ddb_idn = $opus->value("ddb_idn");
$urn_snid = $opus->value("urn_snid");
// Ende Änderung
include ("../../lib/stringValidation.php");
$table = $_REQUEST['table'];
if ($table != $opus_table && $table != $temp_table) {
    die("Fehler in Parameter table");
}
$bereich_id = $_REQUEST['bereich_id'];
if (!_is_valid($bereich_id, 0, 20, "[0-9]*")) {
    die("Fehler in Parameter bereich_id");
}
$jahr = $_REQUEST['jahr'];
if (!_is_valid($jahr, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter jahr");
}
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
$type = $_REQUEST['type'];
if (!_is_valid($type, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter type");
}
# A. Maile, 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$table_autor = $table . "_autor";
$table_inst = $table . "_inst";
$table_diss = $table . "_diss";
$table_schriftenreihe = $table . "_schriftenreihe";
// Anfang Collections
$table_coll = $table . "_coll";
// Ende Collections
$titel = $opus->value("titel_loeschen");
$ueberschrift = $opus->value("ueberschrift_loeschen");
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
if ($table == "$opus_table") {
    // Änderung für URN-Verwaltung
    $res = $opus->query("SELECT title, creator_corporate, contributors_name, date_year, date_creation, source_swb, urn from $table WHERE  source_opus = $source_opus");
    // Ende Änderung
    $mrow = $opus->fetch_row($res);
    $doctitle = $mrow[0];
    $creator_corporate = $mrow[1];
    $contributors_name = $mrow[2];
    $date_year = $mrow[3];
    $jahr = date("Y", $mrow[4]);
    $source_swb = $mrow[5];
    // Änderung für URN-Verwaltung
    $urn = $mrow[6];
    // Ende Änderung
    $opus->free_result($res);
    $res = $opus->query("SELECT creator_name FROM $table_autor where source_opus = $source_opus");
    $mrow = $opus->fetch_row($res);
    $creator_name = $mrow[0];
    $opus->free_result($res);
}
$res = $opus->query("SELECT subject_type from $table WHERE source_opus = $source_opus");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $subject_type = $mrow[0];
} else {
    $subject_type = "";
}
$stat = $opus->query("DELETE FROM $table WHERE source_opus = $source_opus");
$anzahl = $opus->affected_rows();
if ($anzahl == 0) {
    echo ("ERROR-STATUS: $stat.Es wurde kein Datensatz in $table gel&ouml;scht. \n");
    print ("<P><A HREF=\"$home/admin/\">Adminseite</A> \n");
    $design->foot("loeschen.$php", $la);
    exit;
} else {
    /* Eintrag in temp_autor bzw. opus_autor muss geloescht werden */
    $stat_autor = $opus->query("DELETE FROM $table_autor WHERE source_opus = $source_opus");
    /* Eintrag in temp_inst bzw. opus_inst muss geloescht werden */
    $stat_inst = $opus->query("DELETE FROM $table_inst WHERE source_opus = $source_opus");
    $anzahl_inst = $opus->affected_rows();
    if ($anzahl_inst == 0) {
        echo ("ERROR-STATUS: $stat_inst. Es wurde kein Datensatz in $table_inst gel&ouml;scht. \n");
        $design->foot("loeschen.$php", $la);
        exit;
    }
    /* Eintrag in temp_schriftenreihe bzw. opus_schriftenreihe muss geloescht werden */
    $stat_sr = $opus->query("DELETE FROM $table_schriftenreihe WHERE source_opus = $source_opus");
    // Anfang Collections
    if ($coll_anzeigen == "true") {
        /* Eintrag in temp_coll bzw opus_coll muss geloescht werden */
        $stat_coll = $opus->query("DELETE FROM $table_coll WHERE source_opus = $source_opus");
    }
    // Ende Collections
    /* Eintrag in temp_<subject_type> bzw. opus_<subject_type> muss geloescht werden */
    if ($subject_type != "") {
        $table_subject_type = $table . "_" . $subject_type;
        $stat_subject_type = $opus->query("DELETE FROM $table_subject_type WHERE source_opus = $source_opus");
        $anzahl_subject_type = $opus->affected_rows();
        if ($anzahl_subject_type == 0) {
            echo ("ERROR-STATUS: $stat_subject_type. Es wurde kein Datensatz in $table_$subject_type gel&ouml;scht. \n");
            $design->foot("loeschen.$php", $la);
            exit;
        }
    }
    /* Bei Dissertation und Habilitation muss Eintrag auch in temp_diss bzw. opus_diss geloescht werden. */
    if ($type == "8" || $type == "24") {
        $stat_diss = $opus->query("DELETE FROM $table_diss WHERE source_opus = $source_opus");
        $anzahl_diss = $opus->affected_rows();
        if ($anzahl_diss == 0) {
            echo ("ERROR-STATUS: $stat_diss. Es wurde kein Datensatz in $table_diss gel&ouml;scht. \n");
            $design->foot("loeschen.$php", $la);
            exit;
        }
    }
    # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    if ($table == $opus_table) {
        $res = $opus->query("SELECT volltext_pfad, volltext_url FROM bereich_$la WHERE bereich_id = $bereich_id");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $pfad = $mrow[0];
            $volltext_url = $mrow[1];
            $opus->free_result($res);
        }
    } else {
        $pfad = $opus->value("incoming_pfad");
    }
    # Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    if ($table == "$opus_table") {
        $message = "Das Dokument mit der $projekt-IDN $source_opus wurde geloescht.\n\n";
        if ($source_swb != "") {
            $message.= "DC.Source: SWB-IDNR/$source_swb \n";
        }
        $message.= "DC.Title: $doctitle \n";
        if ($creator_name != "") {
            $message.= "DC.Creator_PersonalName: $creator_name \n";
        } else {
            if ($creator_corporate != "") {
                $message.= "DC.Creator_CorporateName: $creator_corporate \n";
            } else {
                if ($contributors_name != "") {
                    $message.= "DC.Contributor.PersonalName: $contributors_name \n";
                }
            }
        }
        $message.= "DC.Date.Creation_of_intellectual_content: $date_year \n";
        $message.= "DC.Identifier: $volltext_url/$jahr/$source_opus/ \n";
        mail($email, "$projekt-IDN $source_opus wurde geloescht", $message, "from: $opusmail");
    }
    print ("<H3>Der Datensatz wurde in der Tabelle $table gel&ouml;scht </H3>");
    if (file_exists("$pfad/$jahr/$source_opus")) {
        $stat_files = $opus->rm("$pfad/$jahr/$source_opus", TRUE);
        if ($stat_files) {
            echo ("<h3>Die Dateien wurden gel&ouml;scht.</h3>");
        } else {
            echo ("ERROR-STATUS: $stat. Die Dateien konnten nicht gel&ouml;scht werden. \n");
            $design->foot("loeschen.$php", $la);
            exit;
        }
    } else {
        print ("<h3>Der Pfad $pfad/$jahr/$source_opus existiert nicht und konnte daher nicht gel&ouml;scht werden.</h3>");
    }
    // Änderung URN-Verwaltung
    if ($urn) { // Nur wenn es eine URN gibt muss folgendes Email an die DB generiert werden
        // Zuerst muss das Attachment geschrieben werden.
        $datei = "xepicur_" . $source_opus . ".xml";
        $pfad_attachment = "$pfad/$datei";
        $xepicur_text = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<epicur xmlns=\"urn:nbn:de:1111-2004033116\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"urn:nbn:de:1111-2004033116 http://nbn-resolving.de/urn/resolver.pl?urn=urn:nbn:de:1111-2004033116\">
		<administrative_data>
			<delivery>	
				<authorization>
					<person_id>$ddb_idn</person_id>
					<urn_snid>$urn_snid</urn_snid>
				</authorization>
				<update_status type=\"url_delete\"/>
			</delivery>
		</administrative_data>
		<record>
			<identifier scheme=\"urn:nbn:de\">$urn</identifier>
			<resource>
				<identifier scheme=\"url\">$volltext_url/$jahr/$source_opus/</identifier>
			</resource>
		</record>
	</epicur>";
        $xml_text = fopen("$pfad_attachment", "w");
        fwrite($xml_text, $xepicur_text);
        fclose($xml_text);
        // Das Mail wird verschickt
        $attachment = fread(fopen("$pfad_attachment", "r"), filesize("$pfad_attachment"));
        $mail->from = "$opusmail";
        $mail->to = "$xepicur_email";
        $mail->subject = "";
        $mail->body = "";
        $mail->add_attachment("$attachment", "$datei", "text/xml");
        $mail->send();
        // und nun muss die xml-Datei "xepicur_$source_opus.xml" gelöscht werden
        unlink("$pfad_attachment");
        // Ende Änderung
        
    }
    print ("<P><A HREF=\"$home/admin/\">Adminseite</A>");
}
$opus->close($sock);
$design->foot("loeschen.$php", $la);
?> 
