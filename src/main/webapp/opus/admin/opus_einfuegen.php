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
 * @version     $Id: opus_einfuegen.php 214 2007-05-17 10:28:15Z freudenberg $
 */
#############################################################
#
# Funktion: admin/opus_einfuegen.php
#           Eintrag der Metadaten in die Opus-Tabelle
#           Volltext wird an die richtige Stelle kopiert
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
# 03.05.2007, Oliver Marahrens, TUB Hamburg-Harburg
# Einfuegen Checksummen
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
$temp_table = $opus->value("temp_table");
$opus_table = $opus->value("opus_table");
$mod_checksum = $opus->value("mod_checksum");
$incoming_pfad = $opus->value("incoming_pfad");
$home = $opus->value("home");
$projekt = $opus->value("projekt");
$email = $opus->value("email");
$opusmail = $opus->value("opusmail");
$urn_alle_doks = $opus->value("urn_alle_doks");
$titel = $opus->value("titel_opus_einfuegen");
$ueberschrift = $opus->value("ueberschrift_opus_einfuegen");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
include ("../../lib/stringValidation.php");
$title = $_REQUEST['title'];
$title = _zeichen_loeschen("[`\n\r]", "", $title);
$title_en = $_REQUEST['title_en'];
$title_en = _zeichen_loeschen("[`\n\r]", "", $title_en);
$title_de = $_REQUEST['title_de'];
$title_de = _zeichen_loeschen("[`\n\r]", "", $title_de);
$creator_name = $_REQUEST['creator_name'];
if (is_array($creator_name)) {
    array_walk($creator_name, '_array_zeichen_loeschen');
} else {
    $creator_name = array();
}
$publisher_inst = $_REQUEST['publisher_inst'];
foreach($publisher_inst as $i) {
    if (!_is_valid($i, 0, 10, "[A-Za-z0-9]*")) {
        die("Fehler in Parameter publisher_inst");
    }
}
$creator_corporate = $_REQUEST['creator_corporate'];
$creator_corporate = _zeichen_loeschen("[`\n\r]", "", $creator_corporate);
$contributors_name = $_REQUEST['contributors_name'];
$contributors_name = _zeichen_loeschen("[`\n\r]", "", $contributors_name);
$contributors_corporate = $_REQUEST['contributors_corporate'];
$contributors_corporate = _zeichen_loeschen("[`\n\r]", "", $contributors_corporate);
$subject_swd = $_REQUEST['subject_swd'];
$subject_swd = _zeichen_loeschen("[`\n\r]", "", $subject_swd);
$subject_uncontrolled_german = $_REQUEST['subject_uncontrolled_german'];
$subject_uncontrolled_german = _zeichen_loeschen("[`\n\r]", "", $subject_uncontrolled_german);
$subject_uncontrolled_english = $_REQUEST['subject_uncontrolled_english'];
$subject_uncontrolled_english = _zeichen_loeschen("[`\n\r]", "", $subject_uncontrolled_english);
$description_lang = $_REQUEST['description_lang'];
if (!_is_valid($description_lang, 0, 3, "[A-Za-z]*")) {
    die("Fehler in Parameter description_lang");
}
$description2_lang = $_REQUEST['description2_lang'];
if (!_is_valid($description2_lang, 0, 3, "[A-Za-z]*")) {
    die("Fehler in Parameter description2_lang");
}
$description = $_REQUEST['description'];
$description = _zeichen_loeschen("[`]", "", $description);
$description2 = $_REQUEST['description2'];
$description2 = _zeichen_loeschen("[`]", "", $description2);
$publisher_university = $_REQUEST['publisher_university'];
$publisher_university = _zeichen_loeschen("[`\n\r]", "", $publisher_university);
$publisher_faculty = $_REQUEST['publisher_faculty'];
if (!_is_valid($publisher_faculty, 0, 5, "[A-Za-z0-9\-]*")) {
    die("Fehler in Parameter publisher_faculty");
}
$advisor = $_REQUEST['advisor'];
$advisor = _zeichen_loeschen("[`\n\r]", "", $advisor);
$type = $_REQUEST['type'];
if (!_is_valid($type, 0, 5, "[0-9]*")) {
    die("Fehler in Parameter type");
}
$date_accepted = $_REQUEST['date_accepted'];
if (!_is_valid($date_accepted, 0, 10, "[0-9\.-]*")) {
    die("Fehler in Parameter date_accepted");
}
$sachgruppe_ddc = $_REQUEST['sachgruppe_ddc'];
if (!_is_valid($sachgruppe_ddc, 0, 10, "[A-Za-z0-9\.]*")) {
    die("Fehler in Parameter sachgruppe_ddc");
}
$language = $_REQUEST['language'];
if (!_is_valid($language, 0, 3, "[A-Za-z]*")) {
    die("Fehler in Parameter language");
}
$isbn = $_REQUEST['isbn'];
if (!_is_valid($isbn, 0, 30, "[0-9xX ,;-]*")) {
    die("Fehler in Parameter isbn");
}
$source_title = $_REQUEST['source_title'];
$source_title = _zeichen_loeschen("[`]", "", $source_title);
$source_swb = $_REQUEST['source_swb'];
if (!_is_valid($source_swb, 0, 10, "[A-Za-z0-9\-]*")) {
    die("Fehler in Parameter source_swb");
}
$verification = $_REQUEST['verification'];
$verification = _zeichen_loeschen("[`\n\r]", "", $verification);
$status = $_REQUEST['status'];
$status = _zeichen_loeschen("[`\n\r]", "", $status);
$date_valid = $_REQUEST['date_valid'];
if (!_is_valid($date_valid, 0, 10, "[0-9\.]*")) {
    die("Fehler in Parameter date_valid");
}
$subject_type = $_REQUEST['subject_type'];
$subject_type = _zeichen_loeschen("[`\n\r]", "", $subject_type);
$subject_value = $_REQUEST['subject_value'];
$subject_value = _zeichen_loeschen("[`\n\r]", "", $subject_value);
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter source_opus");
}
$date_modified = $_REQUEST['date_modified'];
if (!_is_valid($date_modified, 0, 15, "[0-9]*")) {
    die("Fehler in Parameter date_modified");
}
$date_year = $_REQUEST['date_year'];
if (!_is_valid($date_year, 0, 4, "[0-9]*")) {
    die("Fehler in Parameter date_year");
}
$jahr_temp = $_REQUEST['jahr_temp'];
if (!_is_valid($jahr_temp, 4, 4, "[0-9]+")) {
    die("Fehler in Parameter jahr_temp");
}
$sr_id = $_REQUEST['sr_id'];
if (!_is_valid($sr_id, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter sr_id");
}
$sr_band = $_REQUEST['sr_band'];
$sr_band = _zeichen_loeschen("[`\n\r]", "", $sr_band);
$bereich_id = $_REQUEST['bereich_id'];
if (!_is_valid($bereich_id, 0, 20, "[0-9]*")) {
    die("Fehler in Parameter bereich_id");
}
# Falls in Altbestaenden (< Opus 3.0) kein Bereich angegeben ist,
# wird bereich_id auf 1 gesetzt = freier Zugriff auf die Dokumente,
# sonst kommt eine Fehlermeldung der Datenbank
if ($bereich_id == "" || $bereich_id == 0) {
    $bereich_id = 1;
}
$lic = $_REQUEST['lic'];
$lic = _zeichen_loeschen("[`\n\r]", "", $lic);
$bem_intern = $_REQUEST['bem_intern'];
$bem_intern = _zeichen_loeschen("[`]", "", $bem_intern);
$bem_extern = $_REQUEST['bem_extern'];
$bem_extern = _zeichen_loeschen("[`]", "", $bem_extern);
# A. Maile, 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
// Anfang Collections
if ($coll_anzeigen == "true") {
    $collection = $_REQUEST['collection'];
    if (is_array($collection)) {
        array_walk($collection, '_array_zeichen_loeschen');
    } else {
        $collection = array();
    }
}
// Ende Collections
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
if ($sachgruppe_ddc == "no") {
    print ("<H3>Bitte DDC-Sachgruppe anw&auml;hlen!<BR>Bitte mit Back-Button zur&uuml;ck</H3>");
    $design->foot("opus_einfuegen.$php", $la);
    exit();
}
/***** Schriftenreihe Start *****/
if ($sr_id != "" and $sr_band != "") {
    $opus_table_sr = $opus_table . "_schriftenreihe";
    $temp_table_sr = $temp_table . "_schriftenreihe";
    $stat = $opus->query("INSERT INTO $opus_table_sr (source_opus, sr_id, sequence_nr)
        VALUES ('$source_opus', '$sr_id', '$sr_band')");
    if ($stat < 0) {
        print ("ERROR $opus_table_sr: $ERRMSG <p><HR>\n");
    } else {
        $opus->query("DELETE FROM $temp_table_sr where source_opus=$source_opus");
    }
}
/***** Schriftenreihe Stop *****/
/* an die Variable $date_creation wird das Systemdatum uebergeben. */
$date_creation = mktime();
$jahr = date("Y");
$stat = $opus->query("INSERT INTO $opus_table (
		title, creator_corporate, subject_swd, 
		description, publisher_university, 
		contributors_name, contributors_corporate, date_year, 
		date_creation, type, source_opus, source_title,
		source_swb, language, verification, 
		subject_uncontrolled_german, subject_uncontrolled_english, 
		title_en, description2, subject_type, date_valid,
		description_lang, description2_lang, sachgruppe_ddc, 
		bereich_id, lic, isbn, bem_intern, bem_extern)
	VALUES ('$title', '$creator_corporate','$subject_swd', 
		'$description', '$publisher_university',
		'$contributors_name', '$contributors_corporate', '$date_year', 
		'$date_creation', '$type', '$source_opus', '$source_title', 
		'$source_swb', '$language', '$verification', 
		'$subject_uncontrolled_german', '$subject_uncontrolled_english', 
		'$title_en', '$description2', '$subject_type', '$date_valid',
		'$description_lang', '$description2_lang', '$sachgruppe_ddc', 
		'$bereich_id', '$lic', '$isbn', '$bem_intern', '$bem_extern')");
if ($stat < 0) {
    print ("ERROR $opus_table: $ERRMSG <p><HR>\n");
}
$i = 0;
$opus_table_autor = $opus_table . "_autor";
$anzahl_creator_name = sizeof($creator_name);
if ($anzahl_creator_name > 1) {
    while ($i < $anzahl_creator_name) {
        if ($creator_name[$i] != "") {
            $j = $i+1;
            $stat = $opus->query("INSERT INTO $opus_table_autor (source_opus, creator_name, reihenfolge)
	                         VALUES ('$source_opus', '$creator_name[$i]', $j)");
        }
        $i++;
    }
    $autor = $creator_name[0] . " et al.";
} else {
    if ($creator_name[0] != "") {
        $stat = $opus->query("INSERT INTO $opus_table_autor (source_opus, creator_name, reihenfolge)
	                 VALUES ('$source_opus', '$creator_name[0]', 1)");
    }
    $autor = "$creator_name[0]";
}
if ($stat < 0) {
    print ("ERROR $opus_table_autor: $ERRMSG <p><HR>\n");
}
$i = 0;
$opus_table_inst = $opus_table . "_inst";
$anzahl_publisher_inst = sizeof($publisher_inst);
if ($anzahl_publisher_inst > 1) {
    while ($i < $anzahl_publisher_inst) {
        $stat = $opus->query("INSERT INTO $opus_table_inst (source_opus, inst_nr)
                         VALUES ('$source_opus', '$publisher_inst[$i]')");
        $i++;
    }
} else {
    $stat = $opus->query("INSERT INTO $opus_table_inst (source_opus, inst_nr)
                 VALUES ('$source_opus', '$publisher_inst[0]')");
}
if ($stat < 0) {
    print ("ERROR $opus_table_inst: $ERRMSG <p><HR>\n");
}

    # Hashwerte uebertragen
	if ($mod_checksum)
    {
        $i = 0;
        $temp_table_hashes = $temp_table . "_hashes";
        $opus_table_hashes = $opus_table . "_hashes";
        $stat = $opus->query("SELECT * FROM $temp_table_hashes WHERE source_opus='$source_opus'");
        $anzahl_verf = $opus->num_rows($stat);
        if ($anzahl_verf > 0) {
            while ($verf_erg = $opus->fetch_row($stat)) {
                $stat2 = $opus->query("INSERT INTO $opus_table_hashes (source_opus, filename, hash, hash_typ) VALUES ('$verf_erg[0]', '$verf_erg[1]', '$verf_erg[2]', '$verf_erg[3]')");
            }
            $stat3 = $opus->query("DELETE FROM $temp_table_hashes WHERE source_opus='$source_opus'");
        }
    }

// Anfang Collections
if ($coll_anzeigen == "true") {
    $i = 0;
    $opus_table_coll = $opus_table . "_coll";
    $anzahl_collections = sizeof($collection);
    if ($anzahl_collections > 1) {
        while ($i < $anzahl_collections) {
            $stat = $opus->query("INSERT INTO $opus_table_coll( coll_id, source_opus ) 
				VALUES( '$collection[$i]', '$source_opus' ) ");
            $i++;
        }
    } else {
        if ($anzahl_collections == 1) {
            $stat = $opus->query("INSERT INTO $opus_table_coll( coll_id, source_opus ) 
				VALUES( '$collection[0]', '$source_opus' ) ");
        }
    }
    if ($stat < 0) {
        print ("<p>ERROR $opus_table_coll: $ERRMSG <p><hr>\n");
    }
}
// Ende Collections
if ($subject_type != "" && $subject_value != "") {
    $class = explode(",", $subject_value);
    $last = count($class);
    $i = 0;
    $opus_table_subject_type = $opus_table . "_" . $subject_type;
    while ($i < $last) {
        /* fuehrende und nachfolgende Blanks entfernen */
        $class[$i] = trim($class[$i]);
        $stat = $opus->query("INSERT INTO $opus_table_subject_type (source_opus, class)
			VALUES ($source_opus, '$class[$i]')");
        $i++;
    }
}
if ($stat < 0) {
    print ("ERROR $opus_table_$subject_type: $ERRMSG <p><HR>\n");
}
/* Bei Dissertationen wird zusaetzlich Tag der muendlichen Pruefung */
/* und Hauptberichter in der Tabelle opus_diss abgespeichert. */
if ($type == "8" || $type == "24") {
    $opus_table_diss = $opus_table . "_diss";
    $stat = $opus->query("INSERT INTO $opus_table_diss (source_opus, date_accepted, advisor, title_de, publisher_faculty)
                VALUES ('$source_opus', '$date_accepted', '$advisor', '$title_de', '$publisher_faculty')");
}
if ($stat < 0) {
    print ("ERROR: $ERRMSG <p><HR>\n");
} else {
    $temp_table_autor = $temp_table . "_autor";
    $temp_table_inst = $temp_table . "_inst";
    $temp_table_diss = $temp_table . "_diss";
    $temp_table_coll = $temp_table . "_coll";
    $opus->query("DELETE FROM $temp_table where source_opus=$source_opus");
    $opus->query("DELETE FROM $temp_table_autor where source_opus=$source_opus");
    $opus->query("DELETE FROM $temp_table_inst where source_opus =$source_opus");
    $opus->query("DELETE FROM $temp_table_diss where source_opus = $source_opus");
    $opus->query("DELETE FROM $temp_table_coll where source_opus = $source_opus");
    if ($subject_type != "" && $subject_value != "") {
        $temp_table_subject_type = $temp_table . "_" . $subject_type;
        $opus->query("DELETE FROM $temp_table_subject_type where source_opus=$source_opus");
    }
    $res = $opus->query("SELECT volltext_pfad FROM bereich_$la WHERE bereich_id = $bereich_id");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $volltext_pfad = $mrow[0];
    }
    $opus->free_result($res);
    if (!file_exists("$volltext_pfad/$jahr")) {
        mkdir("$volltext_pfad/$jahr");
        chmod("$volltext_pfad/$jahr", 0775);
    }
    if (file_exists("$volltext_pfad/$jahr/$source_opus")) {
        print ("Fehler: $ERRMSG $volltext_pfad/$jahr/$source_opus existiert schon!");
        die;
    } else {
        $ret = rename("$incoming_pfad/$jahr_temp/$source_opus", "$volltext_pfad/$jahr/$source_opus");
	if (!$ret) {
	    print "Verzeichnis $incoming_pfad/$jahr_temp/$source_opus konnte nicht nach $volltext_pfad/$jahr/$source_opus verschoben werden!";
	    die;
	}
    }
    $autor = stripslashes($autor);
    $title = stripslashes($title);
    $message = "Das Dokument mit \n\n";
    $message = $message . "$projekt-IDN      : $source_opus\n";
    $message = $message . "Titel         : $title \n";
    $message = $message . "Autor         : $autor \n\n";
    $message = $message . "wurde von temp nach $opus_table ueberspielt. \n\n";
    $message = $message . "$home/frontdoor.$php?source_opus=$source_opus";
    mail($email, "Ueberspielung von IDN $source_opus in $opus_table: $autor", $message, "from: $opusmail");
    print ("Das Dokument wurde aus dem Zwischenspeicher nach $projekt kopiert und ist jetzt im WWW zug&auml;nglich.");
    if ($urn_alle_doks == 1 && $date_valid == 0) {
        print ("
<P>Bitte noch auf den Knopf <B>URN berechnen</B> dr&uuml;cken (f&uuml;r die DB)
<FORM METHOD = \"POST\" ACTION = \"$url/admin/urn_berechnen.$php\">
<INPUT TYPE = hidden NAME = \"source_opus\" VALUE =\"$source_opus\">
<INPUT TYPE = submit  VALUE=\"URN berechnen\">");
    } else {
        if ($type == "8" || $type == "24") {
            print ("
<P>Bitte noch auf den Knopf <B>URN berechnen</B> dr&uuml;cken (f&uuml;r die DB)
<FORM METHOD = \"POST\" ACTION = \"$url/admin/urn_berechnen.$php\">
<INPUT TYPE = hidden NAME = \"source_opus\" VALUE =\"$source_opus\">
<INPUT TYPE = submit  VALUE=\"URN berechnen\">");
        } else {
            print ("<P>Bitte noch auf den Knopf <B>Indexdatei erstellen</B> dr&uuml;cken (f&uuml;r den Zugang &uuml;ber Suchmaschinen)");
            print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/indexfile.$php\">");
            print ("
<INPUT TYPE = submit  VALUE=\"Indexdatei erstellen\">
<INPUT TYPE = hidden NAME = \"von\" VALUE =\"$source_opus\">
<INPUT TYPE = hidden NAME = \"bis\" VALUE =\"$source_opus\">");
        }
    }
    print ("
</FORM>
<HR>
<CENTER>
");
}
$opus->close($sock);
$design->foot("opus_einfuegen.$php", $la);
?>

