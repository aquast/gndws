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
 * @version     $Id: index.php 236 2007-06-06 08:30:01Z marahrens $
 */
#############################################################
#
# Funktion: admin/index.php
#           Opus-Admin-Homepage
# letzte Änderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
# 06.06.2007, Oliver Marahrens, TUB Hamburg-Harburg
# GPG-Modul: Link zum Keymanager
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$mod_gpg = $opus->value("mod_gpg");
$url = $opus->value("url");
// Anfang Collections
$coll_anzeigen = $opus->value("coll_anzeigen");
// Ende Collections
include ("../../lib/stringValidation.php");
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$titel = $opus->value("titel_admin_index");
$ueberschrift = $opus->value("ueberschrift_admin_index");
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
print ("<UL>
<LI><A HREF=\"/uni/index.php?la=$la\">Neues Dokument in ELBA aufnehmen</A>
<P>
<LI><A HREF=\"./abfrage_aendern.php?table=$temp_table&la=$la\"> Eintrag in der Tabelle TEMP &auml;ndern</A>
<LI><A HREF=\"./temp_browsen.php?la=$la\"> Inhalt der Tabelle TEMP anzeigen</A>
<P>
<LI><A HREF=\"./abfrage_aendern.php?table=$opus_table&la=$la\"> Eintrag in der Tabelle OPUS &auml;ndern</A>
<P>");
// Anfang GPG-Extension
if ($mod_gpg == 1) {
    print ("
	<LI><A HREF=\"./keymanager.php\">Schl&uuml;sselverwaltung</A>
	<P>");
}
// Ende GPG-Extension
// Anfang Collections
if ($coll_anzeigen == "true") {
    print ("
	<LI><A HREF=\"./collections.php?la=$la\"> Collections verwalten</A>
	<P>");
}
// Ende Collections
print ("
<LI><A HREF=\"./indexfile_abfrage.php?la=$la\">Indexdateien erstellen</A>
<LI><A HREF=\"../volltexte/opus-index/opus-indexliste.html\">Liste aller Indexdateien</A>
<P>
<LI><A HREF=\"./statistik_abfrage.php?la=$la\">Ver&ouml;ffentlichungsstatistik</A>
<P>
<LI><A HREF=\"./schriftenreihen.php?la=$la\">Schriftenreihen hinzuf&uuml;gen / &auml;ndern</A>
<P>
<LI><A HREF=\"./link_oai_schnittstelle.php?la=$la\">Link OAI-Schnittstelle</A>
<P>");
print ("
</UL>
");
$design->foot("index.$php", $la)
?>

