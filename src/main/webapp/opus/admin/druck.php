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
 * @version     $Id: druck.php 214 2007-05-17 10:28:15Z freudenberg $
 */
#############################################################
#
# Funktion: admin/db_aendern.php
#           Abspeichern der Metadaten in der Datenbank
# letzte Aenderung:
#
# 20.4.2005, Annette Maile, UB Stuttgart
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
# Schriftenreihen
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$lic_active = $opus->value("license_active");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
include ("../../lib/stringValidation.php");
$table = $_REQUEST['table'];
if ($table != $opus_table && $table != $temp_table) {
    die("Fehler in Parameter table");
}
$source_opus = $_REQUEST['source_opus'];
if (!_is_valid($source_opus, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter source_opus");
}
# A.Maile 10.2.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$table_autor = $table . "_autor";
$table_inst = $table . "_inst";
$table_diss = $table . "_diss";
$table_sr = $table . "_schriftenreihe";
// Anfang collections
$table_coll = $table . "_coll";
// Ende Collections
print ("
<HTML>
<HEAD></HEAD>
<BODY BGCOLOR = FFFFFF>

<FONT COLOR=red>
");
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
$res = $opus->query("SELECT * FROM $table WHERE source_opus = $source_opus");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $title = htmlspecialchars($mrow[0]);
    $creator_corporate = $mrow[1];
    $subject_swd = htmlspecialchars($mrow[2]);
    $description = htmlspecialchars($mrow[3]);
    $publisher_university = $mrow[4];
    $contributors_name = $mrow[5];
    $contributors_corporate = $mrow[6];
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
    $lic = $mrow[27];
    $isbn = $mrow[28];
    $bem_intern = $mrow[29];
    $bem_extern = $mrow[30];
    $opus->free_result($res);
    /* Bei Dissertation (type 8) und Habilitation (type 24) zusaetzlich Fakultaet */
    /* Tag der muendlichen Pruefung (bzw. des Kolloquiums) und Hauptberichter anzeigen */
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
}
if ($num == 0) {
    print ("$suchfeld $suchwert nicht vorhanden!");
} else {
    print ("
<TABLE BORDER = 1> 
<TR>
<TD>Tabelle</TD>");
    print ("<TD>$table</TD>
</TR>

<TR>
<TD>Ident-Nr.</TD>
<TD>$source_opus</TD>
</TR>

<TR>
<TD>Originaltitel</TD>
<TD>$title</TD>
</TR>
");
    if ($title_en != "") {
        print ("
<TR>
<TD>Englischer Titel</TD>
<TD>$title_en</TD>
</TR>
");
    }
    if ($title_de != "") {
        print ("
<TR>
<TD>Deutscher Titel</TD>
<TD>$title_de</TD>
</TR>
</TR>
");
    }
    $autor = $opus->query("SELECT creator_name, reihenfolge FROM $table_autor WHERE source_opus = $source_opus order by reihenfolge");
    $anzahl_creator_name = $opus->num_rows($autor);
    $i = 0;
    if ($anzahl_creator_name > 0) {
        while ($i < $anzahl_creator_name) {
            $mrow = $opus->fetch_row($autor);
            $creator_name = htmlspecialchars($mrow[0]);
            $i++;
            print ("<TR> \n");
            print ("<TD>$i. Verfasser</TD> \n");
            print ("<TD>$creator_name \n");
            print ("</TR> \n");
        }
    }
    $inst = $opus->query("select i.name from institute_$la i, $table_inst oi where oi.source_opus = '$source_opus' and i.nr = oi.inst_nr ");
    $anzahl_inst = $opus->num_rows($inst);
    $i = 0;
    if ($anzahl_inst > 0) {
        while ($i < $anzahl_inst) {
            $mrow = $opus->fetch_row($inst);
            $institut = $mrow[0];
            $i++;
            print ("<TR> \n");
            print ("<TD>Institut $i</TD> \n");
            print ("<TD>$institut \n");
            print ("</TR> \n");
        }
    }
    if ($creator_corporate != "") {
        print ("
<TR>
<TD>Urheber</TD>
<TD>$creator_corporate</TD>
</TR>
");
    }
    if ($contributors_name != "") {
        print ("
<TR>
<TD>Sonst. Beteiligter</TD>
<TD>$contributors_name</TD>
</TR>
");
    }
    if ($contributors_corporate != "") {
        print ("
<TR>
<TD>Sonst. bet. Inst.</TD>
<TD>$contributors_corporate</TD>
</TR>
");
    }
    // Anfang Collections
    if ($coll_anzeigen == "true") {
        $coll_query = "SELECT a.coll_id, b.coll_name FROM $table_coll AS a, collections AS b WHERE a.coll_id = b.coll_id AND a.source_opus = $source_opus;";
        $collection = $opus->query($coll_query);
        $anzahl_collections = $opus->num_rows($collection);
        $i = 0;
        if ($anzahl_collections > 0) {
            while ($i < $anzahl_collections) {
                $mrow = $opus->fetch_row($collection);
                $collection_name = $mrow[1];
                $i++;
                print ("<tr> \n");
                print ("<td>$i. Collection</td>\n");
                print ("<td>$collection_name</td>\n");
                print ("<tr> \n");
            }
        }
        $opus->free_result($collection);
    }
    // Ende Collections
    print ("
<TR>
<TD>Schlagw&ouml;rter SWD</TD>
<TD>$subject_swd</TD>
</TR>
");
    if ($subject_uncontrolled_german != "") {
        print ("
<TR>
<TD>Freie Schlagw&ouml;rter Deutsch</TD>
<TD>$subject_uncontrolled_german</TD>
</TR>
");
    }
    if ($subject_uncontrolled_english != "") {
        print ("
<TR>
<TD>Freie Schlagw&ouml;rter Englisch</TD>
<TD>$subject_uncontrolled_english</TD>
</TR>
");
    }
    if ($subject_type != "") {
        $table_subject_type = $table . "_" . $subject_type;
        $res = $opus->query("SELECT class from $table_subject_type where source_opus = '$source_opus' ");
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
            $opus->free_result($res);
            $res2 = $opus->query("SELECT name from klassifikation_$la where table_name = '$subject_type' ");
            $mrow = $opus->fetch_row($res2);
            $class_name = $mrow[0];
            print ("<TR> \n");
            print ("<TD>$class_name</TD>");
            print ("<TD>$class</TD> \n");
            print ("</TR>");
            $opus->free_result($res2);
        }
    }
    print ("
<TR>
<TD>Erstellungsjahr</TD>
<TD>$date_year</TD>
</TR>
");
    $res = $opus->query("SELECT dokumentart FROM resource_type_$la where typeid = $type");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $dokumentart = $mrow[0];
        print ("<TR> \n");
        print ("<TD>Dokumentart</TD> \n");
        print ("<TD>$dokumentart</TD> \n");
        print ("</TR> \n");
        $opus->free_result($res);
    }
    if ($publisher_faculty != "") {
        $res = $opus->query("SELECT fakultaet from faculty_$la where nr = '$publisher_faculty' ");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $fakultaet = $mrow[0];
            print ("<TR> \n");
            print ("<TD>Fakult&auml;t</TD> \n");
            print ("<TD>$fakultaet</TD> \n");
            print ("</TR> \n");
            $opus->free_result($res);
        }
    }
    if ($advisor != "") {
        print ("
<TR>
<TD>Hauptberichter</TD>
<TD>$advisor</TD>
</TR>
");
    }
    if ($date_accepted != "") {
        $date_accepted_format = strftime("%d.%m.%Y", $date_accepted);
        print ("
<TR>
<TD>Tag der m&uuml;ndl. Pr&uuml;fung</TD>
<TD>$date_accepted_format</TD>
</TR>
");
    }
    $res = $opus->query("SELECT sprache FROM language_$la where code = '$language'");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $sprache = $mrow[0];
        print ("<TR> \n");
        print ("<TD>Sprache</TD> \n");
        print ("<TD>$sprache</TD> \n");
        print ("</TR> \n");
        $opus->free_result($res);
    }
    if ($isbn != "") {
        print ("
<TR>
<TD>ISBN</TD>
<TD>$isbn</TD>
</TR>
");
    }
    if ($source_title != "") {
        print ("
<TR>
<TD>Quelle</TD>
<TD>$source_title</TD>
</TR>
");
    }
    print ("
<TR>
<TD>Adresse</TD>
<TD>$verification</TD>
</TR>
");
    if ($date_valid != 0) {
        print ("<TR>");
        print ("<TD>G&uuml;ltig bis:</TD>");
        $date_valid_format = strftime("%d.%m.%Y", $date_valid);
        print ("<TD>$date_valid_format </TD>");
        print ("</TR>");
    }
    /***** Schriftenreihe Start *****/
    $res = $opus->query("SELECT * FROM $table_sr WHERE source_opus = $source_opus ");
    $num_res = $opus->num_rows($res);
    if ($num_res > 0) {
        $mrow = $opus->fetch_row($res);
        $sr_id = $mrow[1];
        $sr_band = $mrow[2];
        $res = $opus->query("select name from schriftenreihen where sr_id = '$sr_id' ");
        $mrow = $opus->fetch_row($res);
        $sr_name = $mrow[0];
        printf("<tr> \n");
        printf("<td>Schriftenreihe</td> \n");
        printf("<td>$sr_name</td> \n");
        printf("</tr> \n");
        printf("<tr> \n");
        printf("<td>Band</td> \n");
        printf("<td>$sr_band</td> \n");
        printf("</tr> \n");
        $opus->free_result($res);
    }
    /***** Schriftenreihe Stop *****/
    # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    # Falls in Altbestaenden (< Opus 3.0) kein Bereich angegeben ist,
    # wird bereich_id auf 1 gesetzt = freier Zugriff auf die Dokumente,
    # sonst kommt eine Fehlermeldung der Datenbank
    if ($bereich_id == "" || $bereich_id == 0) {
        $bereich_id = 1;
    }
    $res = $opus->query("SELECT bereich FROM bereich_$la WHERE bereich_id = $bereich_id");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $bereich = $mrow[0];
        printf("<tr> \n");
        printf("<td>Zugriffsm&ouml;glichkeit</td> \n");
        printf("<td>$bereich</td> \n");
        printf("</tr> \n");
        $opus->free_result($res);
    }
    # Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    # Start Lizenz
    if ($lic_active > 0) {
        $res = $opus->query("SELECT longname FROM license_$la WHERE shortname = '$lic'");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $lizenz = $mrow[0];
            printf("<tr> \n");
            printf("<td>Lizenz</td> \n");
            printf("<td>$lizenz</td> \n");
            printf("</tr> \n");
            $opus->free_result($res);
        }
    }
    # Ende Lizenz
    $date_creation_format = strftime("%d.%m.%Y", $date_creation);
    $date_modified_old_format = strftime("%d.%m.%Y", $date_modified_old);
    print ("
<TR>
<TD>Publikationsdatum</TD>
<TD>$date_creation_format</TD>
</TR>
");
    if ($date_modified_old != "") {
        print ("
<TR>
<TD>Datum letzte &Auml;nderung</TD>
<TD>$date_modified_old_format</TD>
</TR>
");
    }
    if ($bem_intern != "") {
        print ("
<TR>
<TD>Bermekung intern</TD>
<TD>$bem_intern</TD>
</TR>
");
    }
    if ($bem_extern != "") {
        print ("
<TR>
<TD>Bermekung extern</TD>
<TD>$bem_extern</TD>
</TR>
");
    }
    $res = $opus->query("SELECT sprache FROM language_$la where code = '$description_lang'");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        $mrow = $opus->fetch_row($res);
        $description_sprache = $mrow[0];
        $opus->free_result($res);
    }
    print ("
</TABLE>
<HR>
Kurze Inhaltszusammenfassung in der Originalsprache des Dokuments: $description_sprache<BR>
");
    print (nl2br($description));
    if ($description2 != "") {
        $res = $opus->query("SELECT sprache FROM language_$la where code = '$description2_lang'");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            $mrow = $opus->fetch_row($res);
            $description2_sprache = $mrow[0];
            $opus->free_result($res);
        }
        print ("
<HR>
Kurze Inhaltszusammenfassung in einer weiteren Sprache: $description2_sprache<BR>
");
        print (nl2br($description2));
    }
}
$opus->close($sock);
print ("
</BODY>
</HTML>
");
?>

