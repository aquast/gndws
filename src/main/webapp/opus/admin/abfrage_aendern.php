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
 * @version     $Id: abfrage_aendern.php 189 2007-04-25 11:57:13Z freudenberg $
 */
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$url = $opus->value("url");
include ("../../lib/stringValidation.php");
$table = $_REQUEST['table'];
if ($table != $opus_table && $table != $temp_table) {
    die("Fehler in Parameter table");
}
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
$titel = $opus->value("titel_abfrage_aendern");
$ueberschrift = $opus->value("ueberschrift_abfrage_aendern") . " " . strtoupper($table);
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
print ("<FORM METHOD=GET ACTION=\"$url/admin/admin_browsen.$php?la=$la\"> 

	Suchfeld: 
	<SELECT NAME=\"suchfeld\" SIZE=1> 
		<OPTION VALUE=\"title\">Titel
		<OPTION VALUE=\"creator_name\">Autor 
		<OPTION VALUE=\"source_opus\" SELECTED>$table-IDN
		<OPTION VALUE=\"source_swb\">SWB-Ident-Nr. (PPN)

	</SELECT> 
	<P>
	Suchwert: 
		<INPUT TYPE=\"text\"   NAME=\"suchwert\" SIZE=\"20\" MAXLENGTH=\"20\"> 
		<INPUT TYPE=\"hidden\" NAME=\"table\" VALUE=\"$table\">

	<P> 
		<INPUT TYPE=\"submit\" VALUE=\"Abfrage starten\"> 
		<INPUT TYPE=\"reset\"  VALUE=\"Zuruecksetzen\"> 
	</FORM>
");
$design->foot("abfrage_aendern.$php", $la);
?>

