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
 * @author      Oliver Marahrens <o.marahrens@tu-harburg.de>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id: $
 */
include_once '../../lib/class.checksum.php';

$opus = new OPUS("../../lib/opus.conf");
$dbms = $opus->value("dbms");
$php = $opus->value("php");
$doku_pfad = $opus->value("doku_pfad");
$db = $opus->value("db");
$url = $opus->value("url");
$doku_pfad = $opus->value("doku_pfad");
$temp_table = $opus->value("temp_table");
$opus_table = $opus->value("opus_table");
$bibkey_id = $opus->value("bibkey_id");

$logout = true;

# Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, "de");

$sock = $opus->connect();
if ( $sock < 0 ) {
    echo ("Error: $ERRMSG\n");
}
if ( $opus->select_db($db) < 0 ) {
    echo ("Error: $ERRMSG\n");
}

if ($_POST["keyexport"])
{
	$k = new GPGKey();
	$k->id = $_POST["keyid"];
	$k->export();
}
if ($_POST["keysign"])
{
	$k = new GPGKey();
	$k->id = $_POST["keyid"];
	if ($_POST["sig_passwd"])
	{
		$signing = $k->signKey($_POST["sig_passwd"]);
	}
	else
	{
		$signing = $k->signKey();
	}
	if ($signing === false)
	{
		echo "<span style=\"color:red\">Signatur des Schl&uuml;ssels fehlgeschlagen! Vielleicht beim Passwort vertippt?</span>";
	}
}
if ($_POST["keydelete"])
{
	$k = new GPGKey();
	$k->id = $_POST["keyid"];
	// Nur löschen, wenn dem Key keine Publikationen zugeordnet sind
	// Auch der Systemkey darf hier nicht löschbar sein
	if (count($k->getSignedPublications()) === 0 && $k->id != $bibkey_id)
	{
		$k->delete();
	}
}

if ($_POST["upload"] && $_FILES["keyfile"]['name']) {
    $identifier = $_FILES["keyfile"];
    $k = new GPGKey();
    if ($k->checkKeyfileExtension($identifier) === true) 
    {
        $k->import($identifier['tmp_name']);
    }
}

if (GPG::isInstalled() === false)
{
	die("GPG ist nicht installiert oder in opus.conf ist der falsche Pfad angegeben. Bitte prüfen Sie die GPG-Installation.");
}

print("<h2>GPG-Key-Manager</h2>");

echo "<form name=\"keyform\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" enctype=\"multipart/form-data\">
<table>
<tr>
<td>Schl&uuml;sseldatei</td>
<td><input type=\"file\" name=\"keyfile\"/></td>
</tr>
<!--<tr>
<td>Fingerprint</td>
<td><input type=\"text\" name=\"fingerprint\"/></td>
</tr>-->";
echo "<tr>
<td colspan=\"2\">" .
		"<input type=\"submit\" name=\"upload\" value=\"Key hochladen\"/></td>
</tr>
</table>
</form>";


$s = new GPGKey();
$keys = $s->listKeys();
for ($n = 0; $keys[$n]; $n++)
{
	echo "<div name=\"keyinfo".$keys[$n]->id."\" style=\"padding-bottom:5px;\">" .
			"<form name=\"keyform".$keys[$n]->id."\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\">";
	echo "<input type=\"hidden\" name=\"keyid\" value=\"".$keys[$n]->id."\" />";
	echo "Key f&uuml;r User ".$keys[$n]->owner." (".$keys[$n]->owner_email."): ".$keys[$n]->id."<br/>" .
			"Fingerprint: ".$keys[$n]->getFingerprint();
	if ($keys[$n]->id == $bibkey_id)
	{
		echo "<br/>Dies ist der von OPUS benutzte Systemschl&uuml;ssel!";
	}
	echo "<br/>";
	if ($keys[$n]->expired === true)
	{
		echo "<span style=\"color:red;\">Schl&uuml;ssel ist abgelaufen!</span><br/>";
	}
	if ($keys[$n]->hasBiblSig() === false)
	{
		if (!$keys[$n]->gpg_pass) echo "Signierschl&uuml;sselpasswort: <input type=\"password\" name=\"sig_passwd\" /> ";
		echo "<input type=\"submit\" name=\"keysign\" value=\"Schl&uuml;ssel signieren\" /><br/>";
	}
	// Schlüssel ist exportierbar wenn er 
	// * nicht bereits exportiert ist
	// * durch einen Bibliotheksschlüssel signiert ist
	// * kein Bibliotheksschlüssel ist
	// Alle drei Bedingungen müssen zutreffen
	if ($keys[$n]->isExported() === false && $keys[$n]->hasBiblSig() === true && !in_array($keys[$n]->id, $keys[$n]->bibkeys))
	{
		echo " <input type=\"submit\" name=\"keyexport\" value=\"Schl&uuml;ssel exportieren\" /><br/>";
	}
	// ansonsten hier Link zur Schlüsseldatei anzeigen (sofern vorhanden)
	else if ($keys[$n]->isExported() === true)
	{
		echo "<a href=\"".$keys[$n]->getKeyUrl()."\" target=\"_blank\">Schl&uuml;sseldatei anzeigen</a><br/>";
	}
	$signiert = $keys[$n]->getSignedPublications();
	if (count($signiert) === 0 && $keys[$n]->id != $bibkey_id)
	{
		echo "<input type=\"submit\" name=\"keydelete\" value=\"Schl&uuml;ssel l&ouml;schen\" /><br/>";
	}
	echo "Mit diesem Key sind ".count($signiert)." Publikationen signiert.";
	echo "</form></div>";
}

$design->foot("keymanager.$php", "de");

?>
