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
 * @version     $Id: sachgruppe.php 214 2007-05-17 10:28:15Z freudenberg $
 */
#############################################################
#
# Funktion: admin/sachgruppe.php
#           Auswahl DDC-Sachgruppe
# letzte Aenderung:
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
#
# 29.7.2005, Annette Maile, UB Stuttgart
# Einfuegen Lizenzvertrag
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$table = $opus->value("temp_table");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
$table_diss = $table . "_diss";
$table_autor = $table . "_autor";
$table_inst = $table . "_inst";
// Anfang Collections
$table_coll = $table . "_coll";
// Ende Collections
$url = $opus->value("url");
$projekt = $opus->value("projekt");
$titel = $opus->value("titel_sachgruppe_ddc");
$ueberschrift = $opus->value("ueberschrift_sachgruppe_ddc");
include ("../../lib/stringValidation.php");
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 1, 10, "[0-9]+")) {
    die("Fehler in Parameter source_opus");
}
$jahr_temp = $_REQUEST['jahr_temp'];
if (!_is_valid($jahr_temp, 4, 4, "[0-9]+")) {
    die("Fehler in Parameter jahr_temp");
}
# A. Maile, 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
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
print ("</FONT>\n");
/***** Schriftenreihe Start *****/
$table_sr = $table . "_schriftenreihe";
$res2 = $opus->query("SELECT sr_id, sequence_nr FROM $table_sr WHERE source_opus = '$source_opus'");
$num2 = $opus->num_rows($res2);
if ($num2 > 0) {
    $mrow2 = $opus->fetch_row($res2);
    $sr_id = $mrow2[0];
    $sr_band = $mrow2[1];
}
if ($sr_id == "" xor $sr_band == "") {
    print ("<H3>Schriftenreihe oder Bandnummer fehlt!<BR>Bitte mit Back-Button zur&uuml;ck.</H3>");
    $design->foot("db_aendern.$php", $la);
    exit();
}
/***** Schriftenreihe Stop *****/
$res = $opus->query("SELECT * FROM $table WHERE source_opus = $source_opus");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $title = htmlspecialchars($mrow[0]);
    $creator_corporate = htmlspecialchars($mrow[1]);
    $subject_swd = htmlspecialchars($mrow[2]);
    $description = htmlspecialchars($mrow[3]);
    $publisher_university = $mrow[4];
    $contributors_name = htmlspecialchars($mrow[5]);
    $contributors_corporate = htmlspecialchars($mrow[6]);
    $date_year = $mrow[7];
    $date_creation = $mrow[8];
    $date_modified_old = $mrow[9];
    $type = $mrow[10];
    $source_opus = $mrow[11];
    $source_title = htmlspecialchars($mrow[12]);
    $source_swb = $mrow[13];
    $language = $mrow[14];
    $verification = $mrow[15];
    $subject_uncontrolled_german = htmlspecialchars($mrow[16]);
    $subject_uncontrolled_english = htmlspecialchars($mrow[17]);
    $title_en = htmlspecialchars($mrow[18]);
    $description2 = htmlspecialchars($mrow[19]);
    $subject_type = $mrow[20];
    $date_valid = $mrow[21];
    $description_lang = $mrow[22];
    $description2_lang = $mrow[23];
    $sachgruppe_ddc = $mrow[24];
    # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    $bereich_id = $mrow[26];
    # Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    # Start Lizenzvertrag
    $lic_active = $opus->value("license_active");
    if ($lic_active > 0) {
        $lic = $mrow[27];
    } else {
        $lic = "";
    }
    # Ende Lizenzvertrag
    $isbn = $mrow[28];
    $bem_intern = $mrow[29];
    $bem_extern = $mrow[30];
    $opus->free_result($res);
    if ($type == "8" || $type == "24") {
        $res = $opus->query("SELECT * FROM $table_diss WHERE source_opus = $source_opus");
        $anz = $opus->num_rows($res);
        if ($anz > 0) {
            $mrow = $opus->fetch_row($res);
            $date_accepted = $mrow[1];
            $advisor = htmlspecialchars($mrow[2]);
            $title_de = htmlspecialchars($mrow[3]);
            $publisher_faculty = $mrow[4];
        }
        $opus->free_result($res);
    } else {
        $date_accepted = "";
        $advisor = "";
        $title_de = "";
        $publisher_faculty = "";
    }
    if ($subject_type != "") {
        $table_subject_type = $table . "_" . $subject_type;
        $res = $opus->query("SELECT class from $table_subject_type where source_opus = $source_opus ");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $class = $mrow[0];
            $i = 1;
            while ($i < $num) {
                $i++;
                $mrow = $opus->fetch_row($res);
                $class = "$class , $mrow[0]";
            }
            $subject_value = $class;
        }
        $opus->free_result($res);
    }
}
print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/opus_einfuegen.$php\"> \n");
print ("<INPUT TYPE = hidden NAME=\"title\" VALUE = \"$title\"> \n");
print ("<INPUT TYPE = hidden NAME=\"title_en\" VALUE = \"$title_en\"> \n");
print ("<INPUT TYPE = hidden NAME=\"title_de\" VALUE = \"$title_de\"> \n");
print ("<INPUT TYPE = hidden NAME=\"creator_corporate\" VALUE=\"$creator_corporate\"> \n");
print ("<INPUT TYPE = hidden NAME=\"subject_swd\" VALUE=\"$subject_swd\"> \n");
print ("<INPUT TYPE = hidden NAME=\"description\" VALUE=\"$description\"> \n");
print ("<INPUT TYPE = hidden NAME=\"description2\" VALUE=\"$description2\"> \n");
print ("<INPUT TYPE = hidden NAME=\"publisher_university\" VALUE=\"$publisher_university\"> \n");
print ("<INPUT TYPE = hidden NAME=\"publisher_faculty\" VALUE=\"$publisher_faculty\"> \n");
print ("<INPUT TYPE = hidden NAME=\"contributors_name\" VALUE=\"$contributors_name\"> \n");
print ("<INPUT TYPE = hidden NAME=\"contributors_corporate\" VALUE=\"$contributors_corporate\"> \n");
print ("<INPUT TYPE = hidden NAME=\"date_year\" VALUE=\"$date_year\"> \n");
print ("<INPUT TYPE = hidden NAME=\"jahr_temp\" VALUE=\"$jahr_temp\"> \n");
print ("<INPUT TYPE = hidden NAME=\"type\" VALUE=\"$type\"> \n");
print ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
print ("<INPUT TYPE = hidden NAME=\"isbn\" VALUE=\"$isbn\"> \n");
print ("<INPUT TYPE = hidden NAME=\"source_title\" VALUE=\"$source_title\"> \n");
print ("<INPUT TYPE = hidden NAME=\"source_swb\" VALUE=\"$source_swb\"> \n");
print ("<INPUT TYPE = hidden NAME=\"language\" VALUE=\"$language\"> \n");
print ("<INPUT TYPE = hidden NAME=\"verification\" VALUE=\"$verification\"> \n");
print ("<INPUT TYPE = hidden NAME=\"subject_uncontrolled_german\" VALUE=\"$subject_uncontrolled_german\"> \n");
print ("<INPUT TYPE = hidden NAME=\"subject_uncontrolled_english\" VALUE=\"$subject_uncontrolled_english\"> \n");
print ("<INPUT TYPE = hidden NAME=\"subject_type\" VALUE=\"$subject_type\"> \n");
print ("<INPUT TYPE = hidden NAME=\"subject_value\" VALUE=\"$subject_value\"> \n");
print ("<INPUT TYPE = hidden NAME=\"date_accepted\" VALUE=\"$date_accepted\"> \n");
print ("<INPUT TYPE = hidden NAME=\"advisor\" VALUE=\"$advisor\"> \n");
print ("<INPUT TYPE = hidden NAME=\"date_valid\" VALUE=\"$date_valid\"> \n");
print ("<INPUT TYPE = hidden NAME=\"description_lang\" VALUE=\"$description_lang\"> \n");
print ("<INPUT TYPE = hidden NAME=\"description2_lang\" VALUE=\"$description2_lang\"> \n");
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
print ("<INPUT TYPE = hidden NAME=\"bereich_id\" VALUE=\"$bereich_id\"> \n");
# Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
# Start Lizenzvertrag
print ("<INPUT TYPE = hidden NAME=\"lic\" VALUE=\"$lic\"> \n");
# Ende Lizenzvertrag
print ("<INPUT TYPE = hidden NAME=\"bem_intern\" VALUE=\"$bem_intern\"> \n");
print ("<INPUT TYPE = hidden NAME=\"bem_extern\" VALUE=\"$bem_extern\"> \n");
/*** Schriftenreihe Start ***/
print ("<INPUT TYPE = hidden NAME=\"sr_id\" VALUE=\"$sr_id\"> \n");
print ("<INPUT TYPE = hidden NAME=\"sr_band\" VALUE=\"$sr_band\"> \n");
/*** Schriftenreihe Ende ***/
$autor = $opus->query("SELECT creator_name, reihenfolge FROM $table_autor WHERE source_opus = $source_opus order
by reihenfolge");
$anzahl_creator_name = $opus->num_rows($autor);
$i = 0;
if ($anzahl_creator_name > 0) {
    while ($i < $anzahl_creator_name) {
        $mrow = $opus->fetch_row($autor);
        $creator_name = htmlspecialchars($mrow[0]);
        $i++;
        print ("<INPUT TYPE=\"hidden\" NAME=\"creator_name[]\" VALUE=\"$creator_name\"> \n");
    }
    $opus->free_result($autor);
}
$inst = $opus->query("select inst_nr from $table_inst oi where oi.source_opus = '$source_opus'");
$anzahl_inst = $opus->num_rows($inst);
$i = 0;
if ($anzahl_inst > 0) {
    while ($i < $anzahl_inst) {
        $mrow = $opus->fetch_row($inst);
        $institut = $mrow[0];
        $i++;
        print ("<INPUT TYPE=\"hidden\" NAME=\"publisher_inst[]\" VALUE=\"$institut\"> \n");
    }
    $opus->free_result($inst);
}
// Anfang Collections
if ($coll_anzeigen == "true") {
    $coll = $opus->query("select coll_id from $table_coll where source_opus = '$source_opus'");
    $anzahl_collections = $opus->num_rows($coll);
    $i = 0;
    if ($anzahl_collections > 0) {
        while ($i < $anzahl_collections) {
            $mrow = $opus->fetch_row($coll);
            $coll_id = $mrow[0];
            $i++;
            print ("<input type=\"hidden\" name=\"collection[]\" value=\"$coll_id\"> \n");
        }
        $opus->free_result($coll);
    }
}
// Ende Collections
if ($sachgruppe_ddc == "" || $sachgruppe_ddc == "no") {
    $res = $opus->query("SELECT f.sachgruppe_ddc from faculty_$la f, institute_$la i  where i.nr = $institut and i.fakultaet = f.nr");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $sachgruppe_ddc = $mrow[0];
        $opus->free_result($res);
    }
}
print ("\n<H3>Bitte DDC-Sachgruppe ausw&auml;hlen:</H3>\n");
print ("DDC-Sachgruppe:         ");
$res = $opus->query("SELECT * FROM sachgruppe_ddc_$la");
$num = $opus->num_rows($res);
if ($num > 0) {
    print ("<SELECT NAME=\"sachgruppe_ddc\" SIZE=1> \n");
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res);
        print ("<OPTION VALUE=\"$mrow[0]\"");
        if ($sachgruppe_ddc == "$mrow[0]") {
            print (" SELECTED");
        }
        print ("> $mrow[1] \n");
    }
    print ("</SELECT> \n");
    $opus->free_result($res);
}
print ("<P>\n<INPUT TYPE = submit  VALUE=\"&Uuml;bernahme des Satzes in $projekt-DB\"> \n");
print ("</FORM> \n");
$opus->close($sock);
$design->foot("sachgruppe.$php", $la);
?>

