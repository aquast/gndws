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
 * @version     $Id: aendern.php 359 2007-11-09 11:35:32Z maile $
 */
#############################################################
#
# Funktion: admin/aendern.php
#           Aendern der Metadaten / Upload/Loeschen von Dateien
# letzte Aenderung:
# 20.4.2005, Annette Maile UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
#
# 28.7.2005, Annette Maile, UB Stuttgart
# Einfuegen Lizenzvertrag
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$url = $opus->value("url");
$home = $opus->value("home");
$opt_inst = $opus->value("opt_institute");
$gueltig_anzeigen = $opus->value("gueltig_anzeigen");
$veroeffentlichte_dateien_aendern = $opus->value("veroeffentlichte_dateien_aendern");
$nbn = $opus->value("nbn");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
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
$zusatzautor = $_REQUEST['zusatzautor'];
if (!_is_valid($zusatzautor, 0, 1, "[0-9]*")) {
    die("Fehler in Parameter zusatzautor");
}
$zusatzinst = $_REQUEST['zusatzinst'];
if (!_is_valid($zusatzinst, 0, 1, "[0-9]*")) {
    die("Fehler in Parameter zusatzinst");
}
# A.Maile 10.2.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
// Anfang Collections
if ($coll_anzeigen == "true") {
    $zusatzcoll = $_REQUEST['zusatzcoll'];
    if (!_is_valid($zusatzcoll, 0, 1, "[0-9]*")) {
        die("Fehler in Parameter zusatzcoll");
    }
}
// Ende collections
$projekt = $opus->value("projekt");
$titel_aendern = "titel_" . $table . "_aendern";
$ueberschrift_aendern = "ueberschrift_" . $table . "_aendern";
$titel = $opus->value("$titel_aendern");
$ueberschrift = $opus->value("$ueberschrift_aendern");
$table_autor = $table . "_autor";
$table_inst = $table . "_inst";
$table_diss = $table . "_diss";
// Anfang collections
$table_coll = $table . "_coll";
// Ende collections
# Annette Maile, 20.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
print ("</FONT>");
if ($suchfeld == "source_opus") {
    $res = $opus->query("SELECT * FROM $table WHERE source_opus = $suchwert");
} else {
    if ($suchfeld == "source_swb") {
        $res = $opus->query("SELECT * FROM $table WHERE source_swb = '$suchwert'");
    } else {
        if ($suchfeld == "creator_name") {
            $res = $opus->query("SELECT * FROM $table o, $table_autor oa where oa.creator_name like '$suchwert%' and o.source_opus = oa.source_opus ");
        } else {
            $res = $opus->query("SELECT * FROM $table WHERE $suchfeld LIKE '%$suchwert%'");
        }
    }
}
//Zugriff per $opus-Object auf die Tabelle opus_souce oder temp_source? 
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
    if ($table == $temp_table) {
        $status = $mrow[25];
    } else {
        $urn = $mrow[25];
    }
    # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    $bereich_id = $mrow[26];
    $lic = $mrow[27];
    $isbn = $mrow[28];
    $bem_intern = $mrow[29];
    $bem_extern = $mrow[30];
    $opus->free_result($res);
    /* Bei Dissertation (type 8) und Habilitation (type 24) zusaetzlich */
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
$i = $suchwert;
$vor = $i+1;
$back = $i-1;
if ($zusatzautor == "") {
    $zusatzautor = 0;
}
$zusatzautor = $zusatzautor;
if ($zusatzinst == "") {
    $zusatzinst = 0;
}
$zusatzinst = $zusatzinst;
/* an die Variable $date_modified wird das Systemdatum uebergeben */
$date_modified = mktime();
$jahr = date("Y", $date_creation);
if ($num == 0) {
    include ("../../lib/font.html");
    print ("$suchfeld $suchwert nicht vorhanden!");
    print ("<P><A HREF=\"$home/admin/\">Adminseite</A>");
    print ("</FONT");
} else {
    print ("\n<TABLE BORDER = 0> \n");
    print ("<TR> \n");
    print ("<TD VALIGN = TOP> \n");
    print ("<FORM METHOD=\"POST\" ACTION =\"$url/admin/aendern.$php\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"suchfeld\" VALUE=\"source_opus\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"suchwert\" VALUE=$source_opus > \n");
    $autor_plus = $zusatzautor+1;
    print ("<INPUT TYPE=\"hidden\" NAME=\"zusatzautor\" VALUE =\"$autor_plus\"> \n");
    print ("<INPUT TYPE=\"submit\" VALUE=\"Zus&auml;tzliches Autorenfeld anfordern\"> \n");
    print ("</FORM> \n");
    print ("</TD> \n");
    print ("<TD VALIGN = TOP> \n");
    print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/aendern.$php\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"suchfeld\" VALUE=\"source_opus\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"suchwert\" VALUE=$source_opus > \n");
    $inst_plus = $zusatzinst+1;
    print ("<INPUT TYPE=\"hidden\" NAME=\"zusatzinst\" VALUE=\"$inst_plus\"> \n");
    print ("<INPUT TYPE=\"submit\" VALUE=\"Zus&auml;tzliches Institutsfeld anfordern\"> \n");
    print ("</FORM> \n");
    print ("</TD> \n");
    print ("</TR> \n");
    // Anfang Collections
    if ($coll_anzeigen == "true") {
        print ("<TR> \n");
        print ("<TD VALIGN = TOP> \n");
        print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/aendern.$php\"> \n");
        print ("<INPUT TYPE = hidden NAME = \"table\" VALUE = \"$table\"> \n");
        print ("<INPUT TYPE = hidden NAME = \"suchfeld\" VALUE = \"source_opus\"> \n");
        print ("<INPUT TYPE = hidden NAME = \"suchwert\" VALUE = $source_opus > \n");
        $coll_plus = $zusatzcoll+1;
        print ("<INPUT TYPE = hidden NAME = \"zusatzcoll\" VALUE =\"$coll_plus\"> \n");
        print ("<INPUT TYPE = submit VALUE=\"Zus&auml;tzliches Collectionfeld anfordern\"> \n");
        print ("</FORM> \n");
        print ("</TD> \n");
        print ("</TR> \n");
    }
    // Ende Collections
    print ("</TABLE> \n");
    print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/db_aendern.$php\"> \n");
    print ("* nur Pflicht bei Dissertation und Habilitation. Deutscher Titel nur Pflicht, wenn Originaltitel nicht deutsch ist. \n");
    print ("<TABLE> \n");
    print ("<TR>\n  <TD valign=\"top\">");
    #print("Originaltitel:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"title\" SIZE=60 MAXLENGTH=\"250\" VALUE=\"$title\">");
    print ("Originaltitel:</TD>\n  <TD><TEXTAREA NAME=\"title\" COLS=60 ROWS=2>$title</TEXTAREA>");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD valign=\"top\">");
    #print("Englischer Titel:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"title_en\" SIZE=60 MAXLENGTH=\"250\" VALUE=\"$title_en\">");
    print ("Englischer Titel:</TD>\n  <TD><TEXTAREA NAME=\"title_en\" COLS=60 ROWS=2>$title_en</TEXTAREA>");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD valign=\"top\">");
    #print("Deutscher Titel:(*)</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"title_de\" SIZE=60 MAXLENGTH=\"250\" VALUE=\"$title_de\">");
    print ("Deutscher Titel:(*)</TD>\n  <TD><TEXTAREA NAME=\"title_de\" COLS=60 ROWS=2>$title_de</TEXTAREA>");
    print ("</TD>\n</TR> \n");
    $autor = $opus->query("SELECT creator_name, reihenfolge, pnd_id FROM $table_autor WHERE source_opus = $source_opus order by reihenfolge");
    $anzahl_creator_name = $opus->num_rows($autor);
    $i = 0;
    if ($anzahl_creator_name > 0) {
        while ($i < $anzahl_creator_name) {
            $mrow = $opus->fetch_row($autor);
            $creator_name = htmlspecialchars($mrow[0]);
            $pnd_id = $row[3];
            $i++;
            print ("<TR>\n  <TD>");
            print ("$i. Verfasser:</TD>\n  <TD><INPUT id=\"person\" class=\"person\" TYPE=TEXT NAME=\"creator_name[]\" SIZE=80 MAXLENGTH=200 VALUE=\"$creator_name\"> \n");
            print ("</TD></TR><TR>\n  <TD>");
            print ("PND Identifier:</TD>\n  <TD><INPUT class=\"pndId\" TYPE=TEXT NAME=\"pnd_id[]\" SIZE=20 MAXLENGTH=80 VALUE=\"$pnd_id\"> \n");
            print ("</TD>\n</TR> \n");
        }
        if ($zusatzautor > 0) {
            $num = $anzahl_creator_name+$zusatzautor;
            while ($i < $num) {
                $i++;
                print ("<TR>\n  <TD>");
                print ("$i. Verfasser:</TD>\n  <TD><INPUT id=\"person\"  class=\"person\" TYPE=TEXT NAME=\"creator_name[]\" SIZE=80 MAXLENGTH=200 VALUE=\"\"> \n");
                print ("</TD>\n</TR> \n");
            }
        }
    } else {
        if ($anzahl_creator_name == 0 && $zusatzautor > 0) {
            $i = 0;
            while ($i < $num) {
                $i++;
                print ("<TR>\n  <TD>");
                print ("$i. Verfasser:</TD>\n  <TD><INPUT id=\"person\"  class=\"person\" TYPE=TEXT NAME=\"creator_name[]\" SIZE=80 MAXLENGTH=200 VALUE=\"\"> \n");
                print ("</TD>\n</TR> \n");
            }
        }
    }
    $inst = $opus->query("SELECT inst_nr from $table_inst where source_opus = $source_opus ");
    $anzahl_inst = $opus->num_rows($inst);
    if ($anzahl_inst == 0) {
        $anzahl_inst = 1;
    }
    $res2 = $opus->query("SELECT i.nr, i.name, f.nr, f.fakultaet from institute_$la i, faculty_$la f where i.fakultaet=f.nr order by i.nr");
    $num2 = $opus->num_rows($res2);
    if ($num2 > 0) {
        $i = 0;
        while ($i < $anzahl_inst) {
            $mrow = $opus->fetch_row($inst);
            $inst_nr = $mrow[0];
            $i++;
            print ("<TR>\n  <TD>");
            print ("Institut $i:</TD>\n  <TD>");
            print ("<SELECT NAME=\"publisher_inst[]\" SIZE=1>\n");
            print ("<OPTION VALUE=\"0\">Bitte Institut w&auml;hlen \n");
            $opus->data_seek($res2, 0);
            $fak_alt = "";
            $j = 0;
            while ($j < $num2) {
                $j++;
                $mrow2 = $opus->fetch_row($res2);
                /* Steuerung Institute: wenn Institute angezeigt werden sollen (opt_inst = 1) */
                /* werden Fakultaet und Institut ausgegeben, sonst nur die Fakultaet.         */
                if ($opt_inst > 0) {
                    if ($mrow2[2] != $fak_alt) {
                        print ("<OPTION VALUE=\"0\">$mrow2[3] \n");
                        $fak_alt = $mrow2[2];
                    }
                    print ("<OPTION VALUE=\"$mrow2[0]\" \n");
                    if ($mrow2[0] == $inst_nr) {
                        print (" selected");
                        $fak = $mrow2[2];
                    }
                    print (">&nbsp;&nbsp;- $mrow2[1] \n");
                } else {
                    if ($mrow2[2] == $fak_alt) {
                        print ("<OPTION VALUE=\"0\">$mrow2[3] \n");
                        $fak_alt = $mrow2[2];
                    }
                    print ("<OPTION VALUE=\"$mrow2[0]\" \n");
                    if ($mrow2[0] == $inst_nr) {
                        print (" selected");
                        $fak = $mrow2[2];
                    }
                    print ("> $mrow2[1] \n");
                }
            }
            print ("</SELECT> \n");
            print ("</TD>\n</TR> \n");
        }
        if ($zusatzinst > 0) {
            $num = $anzahl_inst+$zusatzinst;
            while ($i < $num) {
                $i++;
                print ("<TR>\n  <TD>");
                print ("Institut $i:</TD>\n  <TD>");
                print ("<SELECT NAME=\"publisher_inst[]\" SIZE=1>\n");
                print ("<OPTION VALUE=\"0\">Bitte Institut w&auml;hlen \n");
                $opus->data_seek($res2, 0);
                $fak_alt = "";
                $j = 0;
                while ($j < $num2) {
                    $j++;
                    $mrow2 = $opus->fetch_row($res2);
                    /* Steuerung Institute: wenn Institute angezeigt werden sollen (opt_inst = 1) */
                    /* werden Fakultaet und Institut ausgegeben, sonst nur die Fakultaet.         */
                    if ($opt_inst > 0) {
                        if ($mrow2[2] != $fak_alt) {
                            print ("<OPTION VALUE=\"0\">$mrow2[3] \n");
                            $fak_alt = $mrow2[2];
                        }
                        print ("<OPTION VALUE=\"$mrow2[0]\" \n");
                        print (">&nbsp;&nbsp;- $mrow2[1] \n");
                    } else {
                        if ($mrow2[2] == $fak_alt) {
                            print ("<OPTION VALUE=\"0\">$mrow2[3] \n");
                            $fak_alt = $mrow2[2];
                        }
                        print ("<OPTION VALUE=\"$mrow2[0]\" \n");
                        print (">$mrow2[1] \n");
                    }
                }
                print ("</SELECT> \n");
                print ("</TD>\n</TR> \n");
            }
        }
    }
    $opus->free_result($res2);
    print ("<TR>\n  <TD>");
    print ("Urheber:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"creator_corporate\" SIZE=80 MAXLENGTH=200 VALUE=\"$creator_corporate\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Sonst. Beteiligter:</TD>\n  <TD><INPUT id=\"person\"  class=\"person\" TYPE=TEXT NAME=\"contributors_name\" SIZE=80 MAXLENGTH=200 VALUE=\"$contributors_name\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Sonst. bet. Inst.:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"contributors_corporate\" SIZE=80 MAXLENGTH=200 VALUE=\"$contributors_corporate\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Schlagw&ouml;rter <A HREF=\"http://www.bsz-bw.de/cgi-bin/oswd-suche.pl\" target=\"new\">SWD</A>:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"subject_swd\" SIZE=80 MAXLENGTH=150 VALUE=\"$subject_swd\">");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("SW unkontr. deutsch:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"subject_uncontrolled_german\" SIZE=80 VALUE=\"$subject_uncontrolled_german\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("SW unkontr. englisch:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"subject_uncontrolled_english\" SIZE=80 VALUE=\"$subject_uncontrolled_english\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Abstract Originalsprache:</TD>\n  <TD>");
    $sprache = $opus->query("SELECT * FROM language_$la");
    $num = $opus->num_rows($sprache);
    if ($num > 0) {
        print ("<SELECT NAME=\"description_lang\" SIZE=1> \n");
        $i = 0;
        while ($i < $num) {
            $i++;
            $mrow = $opus->fetch_row($sprache);
            print ("<OPTION VALUE=\"$mrow[0]\"");
            if ($mrow[0] == $description_lang) {
                print (" SELECTED");
            }
            print (">$mrow[1] \n");
        }
        print ("</SELECT> \n");
    }
    print ("</TD>\n</TR> \n");
    print ("</TABLE> \n\n");
    print ("<TEXTAREA NAME=\"description\" COLS=80 ROWS=10>$description</TEXTAREA> \n\n");
    print ("<TABLE> \n");
    print ("<TR>\n  <TD>");
    print ("Abstract in einer weiteren Sprache:</TD>\n  <TD>\n<SELECT NAME=\"description2_lang\" SIZE=1>");
    $opus->data_seek($sprache, 0);
    $i = 0;
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($sprache);
        print ("<OPTION VALUE=\"$mrow[0]\"");
        if ($mrow[0] == $description2_lang) {
            print (" SELECTED");
        }
        print (">$mrow[1] \n");
    }
    print ("</SELECT> \n");
    print ("</TD>\n</TR> \n");
    print ("</TABLE> \n\n");
    print ("<TEXTAREA NAME=\"description2\" COLS=80 ROWS=10>$description2</TEXTAREA> \n\n");
    print ("<TABLE> \n");
    print ("<TR>\n  <TD>");
    print ("Universit&auml;t:</TD>\n <TD>");
    $res = $opus->query("SELECT * FROM university_$la");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        print ("<SELECT NAME=\"publisher_university\" SIZE=1> \n");
        $i = 0;
        while ($i < $num) {
            $i++;
            $mrow = $opus->fetch_row($res);
            print ("<OPTION VALUE=\"$mrow[0]\"");
            if ($mrow[0] == $publisher_university) {
                print (" SELECTED");
            }
            print (">$mrow[0] \n");
        }
        print ("</SELECT> \n");
        $opus->free_result($res);
    }
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Erstellungsjahr:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"date_year\" SIZE=4 MAXLENGTH=4 VALUE=\"$date_year\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Dokumentart:</TD>\n  <TD>");
    $res = $opus->query("SELECT typeid, dokumentart FROM resource_type_$la");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        print ("<SELECT NAME=\"type\" SIZE=1> \n");
        $i = 0;
        while ($i < $num) {
            $i++;
            $mrow = $opus->fetch_row($res);
            print ("<OPTION VALUE=\"$mrow[0]\"");
            if ($type == "$mrow[0]") {
                print (" SELECTED> $mrow[1] \n");
            } else {
                print ("> $mrow[1] \n");
            }
        }
        print ("</SELECT> \n");
        $opus->free_result($res);
    }
    print ("</TD>\n</TR> \n");
    if ($opt_inst > 0) {
        print ("<TR>\n  <TD>");
        print ("Fakult&auml;t:*</TD>\n  <TD>");
        $res = $opus->query("SELECT * FROM faculty_$la");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            print ("<SELECT NAME=\"publisher_faculty\" SIZE=1> \n");
            print ("<OPTION VALUE=\"-1\">Fakult&auml;t bitte ausw&auml;hlen \n");
            $opus->data_seek($res, 0);
            $i = 0;
            while ($i < $num) {
                $i++;
                $mrow = $opus->fetch_row($res);
                print ("<OPTION VALUE=\"$mrow[0]\"");
                if ($publisher_faculty == "$mrow[0]") {
                    print (" SELECTED> $mrow[1] \n");
                } else {
                    print ("> $mrow[1] \n");
                }
            }
            print ("</SELECT> \n");
            $opus->free_result($res);
        }
        print ("</TD>\n</TR> \n");
    }
    print ("<TR>\n  <TD>");
    print ("Hauptberichter:*</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"advisor\" SIZE=80 MAXLENGTH=100 VALUE=\"$advisor\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    if ($date_accepted != "") {
        $date_accepted_format = strftime("%d.%m.%Y", $date_accepted);
        print ("m&uuml;ndliche Pr&uuml;fung:*</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"date_accepted\" SIZE=11 MAXLENGTH=11 VALUE=\"$date_accepted_format\"> \n");
    } else {
        print ("m&uuml;ndliche Pr&uuml;fung:*</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"date_accepted\" SIZE=11 MAXLENGTH=11> \n");
    }
    print ("</TD>\n</TR> \n");
    // Anfang Collections
    if ($coll_anzeigen == "true") {
        $query = "SELECT coll_id FROM $table_coll WHERE source_opus = $source_opus ;";
        $coll1 = $opus->query($query);
        $anzahl_coll = $opus->num_rows($coll1);
        $coll_query = "SELECT coll_id, coll_name FROM collections ORDER BY lft;";
        $res2 = $opus->query($coll_query);
        $num2 = $opus->num_rows($res2);
        if ($num2 > 0) {
            $i = 0;
            while ($i < $anzahl_coll) {
                $mrow = $opus->fetch_row($coll1);
                $coll_id = $mrow[0];
                $i++;
                print ("<TR>\n  <TD>");
                print ("Collection $i:</TD>\n  <TD>");
                print ("<select name=\"collection[]\" size=1> \n");
                print ("<option value=\"0\">Bitte Collection w&auml;hlen \n");
                $opus->data_seek($res2, 0);
                $j = 0;
                while ($j < $num2) {
                    $j++;
                    $mrow2 = $opus->fetch_row($res2);
                    $coll_id2 = $mrow2[0];
                    $coll_name = $mrow2[1];
                    // Noch die Eltern herausfinden
                    $query3 = "SELECT a.coll_name, (a.rgt - a.lft) AS height
							FROM collections AS a, collections AS b
							WHERE b.lft BETWEEN a.lft AND a.rgt
							AND b.rgt BETWEEN a.lft AND a.rgt
							AND b.coll_id = '$coll_id2'
							ORDER BY height DESC;";
                    $res3 = $opus->query($query3);
                    $num3 = $opus->num_rows($res3);
                    print ("<option value=\"$coll_id2\" ");
                    if ($coll_id2 == $coll_id) {
                        print (" selected");
                    }
                    print ">";
                    $m = 0;
                    while ($m < $num3) {
                        $mrow3 = $opus->fetch_row($res3);
                        $coll_name3 = $mrow3[0];
                        if ($coll_name3 != $coll_name) {
                            echo "$coll_name3 -> ";
                        } else {
                            echo "$coll_name3 \n";
                        }
                        $m++;
                    }
                }
                print ("</select>\n");
                print ("</TD>\n</TR> \n");
            }
        }
        if ($zusatzcoll > 0) {
            $num = $anzahl_coll+$zusatzcoll;
            while ($i < $num) {
                $mrow = $opus->fetch_row($coll1);
                $coll_id = $mrow[0];
                $i++;
                print ("<TR>\n  <TD>");
                print ("Collection $i:</TD>\n  <TD>");
                print ("<select name=\"collection[]\" size=1> \n");
                print ("<option value=\"0\">Bitte Collection w&auml;hlen \n");
                $opus->data_seek($res2, 0);
                $j = 0;
                while ($j < $num2) {
                    $j++;
                    $mrow2 = $opus->fetch_row($res2);
                    $coll_id2 = $mrow2[0];
                    $coll_name = $mrow2[1];
                    // Noch die Eltern herausfinden
                    $query3 = "SELECT a.coll_name, (a.rgt - a.lft) AS height
							FROM collections AS a, collections AS b
							WHERE b.lft BETWEEN a.lft AND a.rgt
							AND b.rgt BETWEEN a.lft AND a.rgt
							AND b.coll_id = '$coll_id2'
							ORDER BY height DESC;";
                    $res3 = $opus->query($query3);
                    $num3 = $opus->num_rows($res3);
                    print ("<option value=\"$coll_id2\" ");
                    if ($coll_id2 == $coll_id) {
                        print (" selected");
                    }
                    print ">";
                    $m = 0;
                    while ($m < $num3) {
                        $mrow3 = $opus->fetch_row($res3);
                        $coll_name3 = $mrow3[0];
                        if ($coll_name3 != $coll_name) {
                            echo "$coll_name3 -> ";
                        } else {
                            echo "$coll_name3 \n";
                        }
                        $m++;
                    }
                }
                print ("</select>\n");
                print ("</TD>\n</TR> \n");
            }
        }
        $opus->free_result($coll1);
        $opus->free_result($res2);
    }
    // Ende Collections
    print ("<TR>\n  <TD>");
    print ("<A HREF=\"show_ddc.php?la=$la\" target=\"new\">DDC-Sachgruppe:</a></TD>\n  <TD>");
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
            print (">$mrow[0] $mrow[1] \n");
        }
        print ("</SELECT> \n");
        $opus->free_result($res);
    }
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Sprache:</TD>\n  <TD>");
    $res = $opus->query("SELECT * FROM language_$la");
    $num = $opus->num_rows($res);
    if ($num > 0) {
        print ("<SELECT NAME=\"language\" SIZE=1> \n");
        $i = 0;
        while ($i < $num) {
            $i++;
            $mrow = $opus->fetch_row($res);
            print ("<OPTION VALUE=\"$mrow[0]\"");
            if ($language == "$mrow[0]") {
                print (" SELECTED> $mrow[1] \n");
            } else {
                print ("> $mrow[1] \n");
            }
        }
        print ("</SELECT> \n");
        $opus->free_result($res);
    }
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("ISBN:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"isbn\" SIZE=30 MAXLENGTH=30 VALUE=\"$isbn\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Quelle:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"source_title\" SIZE=80 MAXLENGTH=300 VALUE=\"$source_title\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("SWB-Ident-Nr. (PPN):</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"source_swb\" SIZE=10 MAXLENGTH=10 VALUE=\"$source_swb\"> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Email:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"verification\" SIZE=80 MAXLENGTH=200 VALUE=\"$verification\"> \n");
    print ("<TR>\n  <TD>");
    if ($gueltig_anzeigen) {
        print ("<TR>\n  <TD>");
        if ($date_valid > 0 || $date_valid == NULL) {
            $date_valid_format = strftime("%d.%m.%Y", $date_valid);
            print ("G&uuml;ltig bis:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"date_valid\" SIZE=11 MAXLENGTH=11 VALUE=\"$date_valid_format\"> (Format 31.01.2000, 0 f&uuml;r unbeschr&auml;nkt) \n");
        } else {
            print ("G&uuml;ltig bis:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"date_valid\" SIZE=11 MAXLENGTH=11 VALUE=\"$date_valid\"> (Format 31.01.2000, 0 f&uuml;r unbeschr&auml;nkt) \n");
        }
        print ("<TR>\n  <TD>");
    } else {
        print (" <INPUT TYPE=\"hidden\"  NAME=\"date_valid\" VALUE=\"0\">");
    }
    if ($subject_type != "") {
        print ("<INPUT TYPE=\"hidden\" NAME=\"subject_type\" VALUE=\"$subject_type\">");
        print ("<TR>\n  <TD>");
        $res = $opus->query("SELECT name from klassifikation_$la where table_name = '$subject_type' ");
        $mrow = $opus->fetch_row($res);
        $class_name = $mrow[0];
        $opus->free_result($res);
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
            $opus->free_result($res);
            $res = $opus->query("SELECT name from klassifikation_$la where table_name = '$subject_type' ");
            $mrow = $opus->fetch_row($res);
            $class_name = $mrow[0];
            print ("$class_name:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"subject_value\" SIZE=80 MAXLENGTH=100 VALUE=\"$class\"> \n");
        } else {
            print ("$class_name:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"subject_value\" SIZE=80 MAXLENGTH=100> \n");
        }
        $opus->free_result($res);
        print ("</TD>\n</TR> \n");
    } else {
        print ("<INPUT TYPE=\"hidden\" NAME=\"subject_type\" VALUE=\"\"> \n");
    }
    /***** Schriftenreihe Start *****/
    print ("<TR>\n  <TD>");
    print ("Schriftenreihe:</TD>\n  <TD>");
    $table_sr = $table . "_schriftenreihe";
    $sr_id = "";
    $sr_band = "";
    $res = $opus->query("SELECT sr_id, sequence_nr FROM $table_sr where source_opus=$source_opus");
    $num = $opus->num_rows($res);
    $mrow = $opus->fetch_row($res);
    $sr_id = $mrow[0];
    $sr_band = $mrow[1];
    $res2 = $opus->query("SELECT sr_id, name FROM schriftenreihen ORDER by name");
    $num = $opus->num_rows($res2);
    $i = 0;
    print ("<SELECT NAME=\"sr_id\" SIZE=1>\n");
    print ("<OPTION VALUE=\"\">keine Schriftenreihe \n");
    while ($i < $num) {
        $i++;
        $mrow = $opus->fetch_row($res2);
        print ("<OPTION VALUE='$mrow[0]'");
        if ($sr_id == "$mrow[0]") {
            print (" SELECTED> $mrow[1] \n");
        } else {
            print ("> $mrow[1] \n");
        }
    }
    print ("</SELECT> \n");
    print ("</TD>\n</TR> \n");
    print ("<TR>\n  <TD>");
    print ("Bandnummer:</TD>\n  <TD><INPUT TYPE=TEXT NAME=\"sr_band\" VALUE=\"$sr_band\" SIZE=10 MAXLENGTH=10> ");
    print ("</TD>\n</TR> \n");
    $opus->free_result($res2);
    $opus->free_result($res);
    /***** Schriftenreihe Stop *****/
    # Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    print ("<TR>\n  <TD>");
    printf("Zugriffsbeschr&auml;nkung:</TD>\n  <TD>");
    if ($table == "$temp_table") { // Nur so lange ein Dokument den Temp-status hat ist es
        // moeglich zwischen campusweit und weltweit zu waehlen
        $res = $opus->query("SELECT * FROM bereich_$la");
        $num = $opus->num_rows($res);
        if ($num > 0) {
            printf("<select name=\"bereich_id\" size=1>\n");
            $i = 0;
            while ($i < $num) {
                $i++;
                $mrow = $opus->fetch_row($res);
                printf("<option value=\"$mrow[0]\"");
                if ($bereich_id == "$mrow[0]") {
                    printf(" selected>$mrow[1]\n");
                } else {
                    printf(">$mrow[1] \n");
                }
            }
            printf("</select>\n");
            $opus->free_result($res);
        }
        $volltext_pfad = $opus->value("incoming_pfad");
        $volltext_url = $opus->value("incoming_url");
    } else {
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
            printf("$mrow[0]");
            $volltext_pfad = $mrow[1];
            $volltext_url = $mrow[2];
        }
        $opus->free_result($res);
        print ("\n<INPUT TYPE=\"hidden\" NAME=\"bereich_id\" VALUE=\"$bereich_id\"> \n");
    }
    print ("</TD>\n</TR> \n");
    # Ende Zugriff auf Volltexte weltweit/campusweit/weitere Bereiche
    if (file_exists("$volltext_pfad/$jahr/$source_opus/index.html") == 1) {
        print ("<TR>\n  <TD>");
        print ("Volltext:</TD>\n  <TD><A HREF=\"$volltext_url/$jahr/$source_opus/\">$source_opus-index.html</A> \n");
        print ("</TD>\n</TR> \n");
    }
    print ("<TR>\n  <TD>");
    $date_creation_format = strftime("%d.%m.%Y", $date_creation);
    print ("Publikationsdatum:</TD>\n  <TD>$date_creation_format \n");
    print ("</TD>\n</TR> \n");
    if ($date_modified_old != NULL) {
        print ("<TR>\n  <TD>");
        $date_modified_old_format = strftime("%d.%m.%Y", $date_modified_old);
        print ("Datum letzte &Auml;nderung:</TD>\n  <TD>$date_modified_old_format \n");
        print ("</TD>\n</TR> \n");
    }
    if ($urn != "") {
        print ("<TR>\n  <TD>");
        print ("URN:</TD>\n  <TD>$urn \n");
        print ("</TD>\n</TR> \n");
    }
    print ("<TR>\n  <TD>");
    print ("$projekt-Ident-Nr.:</TD>\n  <TD>$source_opus \n");
    print ("</TD>\n</TR> \n");
    # Start Lizenzvertrag
    $lic_active = $opus->value("license_active");
    if ($lic_active > 0) {
        $admin_active_only = $opus->value("admin_active_only");
        if ($admin_active_only) {
            $licsql = "SELECT shortname,longname,link from license_$la where active = 1 order by shortname";
        } else {
            $licsql = "SELECT shortname,longname,link from license_$la order by shortname";
        }
        $licres = $opus->query($licsql);
        $licnum = $opus->num_rows($licres);
        if ($licnum > 0) {
            $lic_adminshow = $opus->value("license_admin");
            print ("<TR>\n  <TD>");
            print ("<a href=\"$lic_adminshow\" target=\"_blank\">Lizenz / Vertrag:</a></TD>\n");
            print ("  <TD><select name=\"lic\" size=\"1\">\n");
            print ("<option value=\"\">(kein Vertrag)</option>\n");
            $licount = 0;
            $lichit = 0;
            while ($licount < $licnum) {
                $licount++;
                $licrow = $opus->fetch_row($licres);
                $licname = $licrow[0];
                if ($licname == $lic) {
                    print ("<option selected value=\"$licname\">");
                    $lichit = 1;
                } else {
                    print ("<option value=\"$licname\">");
                }
                print $licrow[1];
                print ("</option>\n");
            }
            print ("</select>\n");
            if ($lichit == 0) {
                if (!$lic) {
                    $lic_err = "NULL";
                } else {
                    $lic_err = $lic;
                }
                print ("<small><font color=\"red\">\n&nbsp;Keine Lizenz ($lic_err).\n");
                print ("(license_active=" . $lic_active . ").</font></small>\n");
            }
            print ("</TD>\n</TR> \n");
        }
    }
    # Ende Lizenzvertrag
    if ($table == $temp_table) {
        print ("<TR>\n  <TD>");
        print ("Status:</TD>\n  <TD><SELECT NAME=\"status\" SIZE=1> \n
<OPTION SELECTED>$status");
        if ($status != "neu") {
            print ("<OPTION>neu");
        }
        if ($status != "in Bearbeitung") {
            print ("<OPTION>in Bearbeitung");
        }
        if ($status != "o.k.") {
            print ("<OPTION>o.k.");
        }
        print ("</SELECT> \n");
        print ("</TD>\n</TR> \n");
    }
    print ("</TABLE> \n");
    print ("\n<P>Bemerkung intern:<BR> \n");
    print ("<textarea NAME=\"bem_intern\" COLS=80 ROWS=4>$bem_intern</textarea> \n");
    print ("\n<P>Bemerkung extern:<BR> \n");
    print ("<textarea NAME=\"bem_extern\" COLS=80 ROWS=4>$bem_extern</textarea> \n");
    print ("\n<P> \n");
    print ("<INPUT TYPE=HIDDEN NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
    print ("<INPUT TYPE=HIDDEN NAME=\"date_modified\" VALUE=\"$date_modified\"> \n");
    print ("<INPUT TYPE=HIDDEN NAME=\"table\" VALUE=\"$table\"> \n");
    print ("<TABLE BORDER = 0> \n");
    print ("<TR> \n");
    print ("<TD VALIGN = TOP> \n");
    print ("<INPUT TYPE=SUBMIT NAME=\"Aendern\" VALUE=\"&Auml;ndern\">");
    print ("</FORM> \n\n");
    print ("</TD> \n\n");
    print ("<TD VALIGN = TOP> \n");
    print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/druck.$php\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
    print ("<INPUT TYPE=SUBMIT VALUE=\"Drucken\"> \n");
    print ("</FORM> \n");
    print ("</TD> \n\n");
    if ($table == "$temp_table") {
        $nbn = $nbn . $source_opus;
        print ("<TD VALIGN = TOP> \n");
        print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/nbn-pruefziffer.$php\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"NBN\" VALUE=\"$nbn\"> \n");
        print ("<input type=\"submit\" name=\"berechnen\" value=\"URN berechnen\"> \n");
        print ("</FORM> \n");
        print ("</TD> \n\n");
    } else {
        print ("<TD VALIGN = TOP> \n");
        print ("<FORM METHOD = \"POST\" ACTION = \"$url/admin/urn_berechnen.$php\"> \n");
        print ("<INPUT TYPE = hidden NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
        print ("<input type=submit name=\"berechnen\" value=\"URN eintragen\"> \n");
        print ("</FORM> \n");
        print ("</TD> \n\n");
    }
    print ("</TR> \n");
    print ("<TR> \n");
    print ("<TD VALIGN = TOP> \n");
    print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/abfrage_loeschen.$php\"> \n");
    print ("<INPUT TYPE=\"submit\"  VALUE=\"L&ouml;schen\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"title\" VALUE =\"$title\"> \n");
    if ($anzahl_creator_name > 0) {
        $opus->data_seek($autor, 0);
        $mrow = $opus->fetch_row($autor);
        $creator_name = htmlspecialchars($mrow[0]);
        $i = 1;
        while ($i < $anzahl_creator_name) {
            $i++;
            $mrow = $opus->fetch_row($autor);
            $mrow[0] = htmlspecialchars($mrow[0]);
            $creator_name = "$creator_name ; $mrow[0]";
        }
        print ("<INPUT TYPE = hidden NAME = \"creator_name\" VALUE =\"$creator_name\"> \n");
    }
    print ("<INPUT TYPE=\"hidden\" NAME=\"bereich_id\" VALUE=\"$bereich_id\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"jahr\" VALUE=\"$jahr\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
    print ("<INPUT TYPE=\"hidden\" NAME=\"type\" VALUE=\"$type\"> \n");
    print ("</FORM> \n");
    print ("</TD> \n");
    if ($table == $temp_table) {
        print ("<TD VALIGN = TOP colspan=2> \n");
        print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/sachgruppe.$php\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"jahr_temp\" VALUE=\"$jahr\"> \n");
        print ("<INPUT TYPE=\"submit\"  VALUE=\"&Uuml;bernahme des Satzes in $opus_table\"> \n");
    } else {
        print ("<TD VALIGN = TOP> \n");
        if ($veroeffentlichte_dateien_aendern == 1) {
            print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/dateimanager.$php\"> \n");
            print ("<INPUT TYPE=\"submit\"  VALUE=\"Dateimanager\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"creator_name\" VALUE=\"$creator_name\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"title\" VALUE = \"$title\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"jahr\" VALUE=\"$jahr\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
            print ("</FORM> \n");
        } else {
            print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/indexfile.$php\"> \n");
            print ("<INPUT TYPE=\"submit\"  VALUE=\"Indexdatei erstellen\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"von\" VALUE =\"$source_opus\"> \n");
            print ("<INPUT TYPE=\"hidden\" NAME=\"bis\" VALUE =\"$source_opus\"> \n");
            print ("</FORM> \n");
        }
        print ("</TD> \n");
        print ("<TD VALIGN = TOP> \n");
        print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/bereiche_auflisten.$php\"> \n");
        print ("<INPUT TYPE=\"submit\"  VALUE=\"Zugriffsbeschr&auml;nkung &auml;ndern\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"jahr\" VALUE=\"$jahr\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"bereich_id\" VALUE=\"$bereich_id\"> \n");
    }
    print ("</FORM> \n");
    print ("</TD> \n");
    print ("</TR> \n");
    if ($table == "$temp_table") {
        print ("<TR> \n");
        print ("<TD VALIGN = TOP> \n");
        print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/dateimanager.$php\"> \n");
        print ("<INPUT TYPE=\"submit\"  VALUE=\"Dateimanager\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"creator_name\" VALUE=\"$creator_name\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"title\" VALUE = \"$title\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"jahr\" VALUE=\"$jahr\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\"> \n");
        print ("</FORM> \n");
        print ("</TD> \n");
        print ("<TD VALIGN = TOP colspan=2> \n");
        print ("<FORM METHOD=\"POST\" ACTION=\"$url/admin/klassifikationen_auflisten.$php\"> \n");
        print ("<INPUT TYPE=\"hidden\" NAME=\"source_opus\" VALUE=\"$source_opus\"> \n");
        print ("<INPUT TYPE=\"submit\"  VALUE=\"Klassifikationsfeld anlegen\"> \n");
        print ("</FORM> \n");
        print ("</TD> \n");
        print ("</TR> \n");
    }
    print ("</TABLE> \n");
    print ("<HR> \n");
    print ("
<TABLE border=\"1\">
<TD colspan = \"2\"><b>Hinweise zu den Buttons</b></TD> 
<TR>
  <TD>&Auml;ndern</TD>
  <TD>Wenn die Metadaten ge&auml;ndert werden, werden sie durch Dr&uuml;cken des Knopfes \"<i>&Auml;ndern</i>\" gespeichert. Danach nicht mit dem Back-Button des Browsers zur&uuml;ckkehren, sondern auf der Folgeseite den Knopf \"<i>Zur&uuml;ck zum aktualisierten Datensatz</i>\" klicken.</TD>
</TR>
<TR>
  <TD>Drucken</TD>
  <TD>Der Knopf \"<i>Drucken</i>\" gibt die Daten so aus, damit sie ausgedruckt werden k&ouml;nnen (z.B. sind die Abstracts komplett sichtbar).</TD>
</TR>
<TR>
");
    if ($table == "$temp_table") {
        print ("
  <TD>URN berechnen</TD>
  <TD>Mit dem Knopf \"<i>URN berechnen</i>\" kann man vorab die URN berechnen, die das Dokument haben wird. Man kann die URN dann dem Autor mitteilen und er kann Sie in sein Dokument eintragen. Danach muss die Datei neu auf den Server kopiert werden (Knopf \"<i>Dateimanager</i>\") Die URN kann nicht vor dem Eintragen in die Datenbank berechnet werden, da die Opus-Identnr. erst im Moment des Eintragens vergeben wird und erst dann kann die URN berechnet werden.</TD>
  ");
    } else {
        print ("
  <TD>URN eintragen</TD>
  <TD>Mit dem Knopf \"<i>URN eintragen</i>\" kann die URN in die Datenbank eingetragen werden. Dies ist in der Regel nur notwendig, wenn man nicht f&uuml;r alle Dokumentarten URNs vergeben hat und das dann aber &auml;ndern m&ouml;chte. </TD>
  ");
    }
    print ("
</TR>

<TR>
  <TD>L&ouml;schen</TD>
  <TD>Durch Dr&uuml;cken des Knopfes \"<i>L&ouml;schen</i>\" werden die Metadaten aus der Datenbank und die dazu geh&ouml;rigen Dateien gel&ouml;scht.</TD>
</TR>
");
    if ($table == "$temp_table") {
        print ("
<TR>
  <TD>&Uuml;bernahme des Satzes in $opus_table</TD>
  <TD>Mit diesem Knopf wird ein Dokument vom tempor&auml;ren Bereich in den offiziellen Bereich &uuml;berspielt.</TD>
</TR>
<TR>
  <TD>Dateimanager</TD>
  <TD>Mit dem Knopf \"<i>Dateimanager</i>\" k&ouml;nnen Dateien auf den Server neu aufgespielt und gel&ouml;scht werden.</TD>
</TR>
<TR>
  <TD>Klassifikationsfeld anlegen</TD>
  <TD>Mit dem Knopf \"<i>Klassifikationsfeld anlegen</i>\" kann ein leeres Klassifikationsfeld angelegt werden. Danach k&ouml;nnen die Notationen eingegeben werden und mittels des Knopfes \"<i>&Auml;ndern</i>\" gespeichert werden. Wenn ein Klassifikationsfeld gel&ouml;scht werden soll, muss der Inhalt des Klassifikationsfeldes entfernt werden und durch  \"<i>&Auml;ndern</i>\" gespeichert werden.</TD>
</TR>
");
    } else {
        print ("
<TR>
  <TD>Indexdatei erstellen</TD>
  <TD>Wenn Metadaten ge&auml;ndert werden, muss die statische Indexseite neu erstellt werden.</TD>
</TR>
<TR>
  <TD>Zugriffsbeschr&auml;nkung &auml;ndern</TD>
  <TD>Hier kann ein Dokument eine neue Zugriffsbeschr&auml;nkung erhalten (z.B. nur noch innerhalb des Campus zug&auml;nglich).</TD>
</TR>
");
    }
    print ("</TABLE> \n");
}
$opus->close($sock);
$design->foot("aendern.$php", $la);
?>

