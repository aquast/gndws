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
 * @version     $Id: sr_hinzufuegen.php 189 2007-04-25 11:57:13Z freudenberg $
 */
#############################################################
# letzte Änderung:
#
# 22.4.2005, Annette Maile, UB Stuttgart
# Auslagerung Design in lib/design.php
# In Abhaengigkeit von $la kann das entsprechende Design eingelesen werden.
#
#############################################################
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$db = $opus->value("db");
$url = $opus->value("url");
$titel = "Schriftenreihen: Neue Schriftenreihe hinzuf&uuml;gen";
$ueberschrift = $titel;
include ("../../lib/stringValidation.php");
# A.Maile 22.4.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
# Annette Maile, 22.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);
?>
<p>
Bitte den Namen der neuen Schriftenreihe eingeben:
<form method="post" action="sr_add.php" name="AddForm">
<input type="text" name="new_sr" size="60" maxlength="255">
<?php
print ("<input type=\"hidden\" name=\"la\" value=\"$la\">");
?>
<input type="submit" value="Hinzuf&uuml;gen">
</form>
<script type="text/javascript">
<!--
document.AddForm.new_sr.focus();
//-->
</script>
<p>

<?php
print ("
<UL>
<LI><A HREF=\"schriftenreihen.php?la=$la\">Zur&uuml;ck</A></LI>
</UL>
");
$design->foot("sr_hinzufuegen.$php", $la)
?>
