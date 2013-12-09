<?php
/**
 * Bookmarking-Moeglichkeit zu Connotea
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
 * @author      Oliver Marahrens <o.marahrens@tu-harburg.de>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id$
 */

require '../../lib/opus.class.php';

$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$table = $opus->value("opus_table");
$url = $opus->value("url");
$url_anzeigen = $opus->value("url_anzeigen");
$urn_anzeigen = $opus->value("urn_anzeigen");
$lic_active = $opus->value("license_active");
$projekt = $opus->value("projekt");
$doku_pfad = $opus->value("doku_pfad");
$email = $opus->value("email");
$home = $opus->value("home");
$ort = $opus->value("ort");
$ddb_idn = $opus->value("ddb_idn");
$ddb_email_melden = $opus->value("ddb_email_melden");
$ddb_email = $opus->value("ddb_email");
$tar_pfad = $opus->value("tar_pfad");
$gzip_pfad = $opus->value("gzip_pfad");
$titel = $opus->value("titel_indexfile");
$ueberschrift = $opus->value("ueberschrift_indexfile");
$statistik = $opus->value("statistik");
$awstats_url = $opus->value("awstats_url");
$awstats_config = $opus->value("awstats_config");
$empfehlen = $opus->value("empfehlen");
$autoren_login = $opus->value("autoren_login");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
include ("../../lib/stringValidation.php");
$von = $_REQUEST['von'];
if (!_is_valid($von, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter von");
}
$bis = $_REQUEST['bis'];
if (!_is_valid($bis, 0, 10, "[0-9]*")) {
    die("Fehler in Parameter bis");
}
# A.Maile 10.2.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
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
print ("</FONT>");
# O.Marahrens 12.11.06: Connotea-Schnittstelle
$connotea_export = $opus->value("connotea_export");
if ($connotea_export == 1) {
    include_once ("../../lib/class.connotea.php");
    # O. Marahrens: Automatisch alle Texte aus der Textdatei holen
    $connotea_texte = new OPUS("../../texte/$la/connotea.conf");
    foreach($connotea_texte->getValues() as $k => $v) {
        $$k = $v;
    }
    print ("<P>\n");
    print ("<HR>\n");
    include ("../../lib/font.html");
    print ("<H3>$ueberschrift_connotea</H3>\n");
    if ($_POST["connotea_bm"]) {
        $params = array('uri' => $_POST["uri"], 'tags' => $_POST["system_tags"] . " " . $_POST["user_tags"], 'usertitle' => $_POST["usertitle"], 'description' => $_POST["userdescription"]);
        $connoteaPost = new Connotea;
        $connoteaPost->user = $opus->value("connoteauser");
        $connoteaPost->password = $opus->value("connoteapassword");
        $post_status = $connoteaPost->addBookmark($params);
        if ($post_status) {
            echo $add_success;
        } else {
            echo $add_failure;
        }
    }
}
print ("<P><A HREF=\"$home/admin/\">Adminseite</A>");
$opus->close($sock);
$design->foot("connotea_einfuegen.$php", $la);
?>

