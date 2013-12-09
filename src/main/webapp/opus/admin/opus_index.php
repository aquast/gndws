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
 * @version     $Id: opus_index.php 214 2007-05-17 10:28:15Z freudenberg $
 */
require '../../lib/opus.class.php';
$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value("php");
$url = $opus->value("url");
$db = $opus->value("db");
$opus_table = $opus->value("opus_table");
$temp_table = $opus->value("temp_table");
$projekt = $opus->value("projekt");
# A.Maile 10.12.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value("la");
}
# Erzeugung einer Liste aller URLs der Indexdateien von OPUS fuer Suchmaschinen
$sock = $opus->connect();
if ($sock < 0) {
    echo ("Error: $ERRMSG\n");
}
if ($opus->select_db() < 0) {
    echo ("Error: $ERRMSG\n");
}
$res = $opus->query("SELECT volltext_pfad, volltext_url FROM bereich_$la WHERE bereich_id = 1");
$num = $opus->num_rows($res);
if ($num > 0) {
    $mrow = $opus->fetch_row($res);
    $volltext_pfad = $mrow[0];
    $volltext_url = $mrow[1];
    $opus->free_result($res);
}
$opus->close($sock);
$handle = opendir("$volltext_pfad");
while ($file = readdir($handle)) {
    if ($file != "." && $file != "..") {
        $dir_array[count($dir_array) ] = $file;
    }
}
closedir($handle);
sort($dir_array);
$seite = 1;
$fd = fopen("$volltext_pfad/opus-index/opus-indexliste-$seite.html", "w");
fwrite($fd, "<html>\n<head>\n");
fwrite($fd, "<title>Liste aller Indexdateien der Volltexte von $projekt</title>\n</head>\n<body>\n");
fwrite($fd, "<h3>Liste aller Indexdateien der Volltexte von $projekt</h3>\n");
fwrite($fd, "Diese Seite ist für die Indexierung durch Suchmaschinen gedacht.<br>\n");
$i = 0;
$anz = 0;
$num = count($dir_array);
while ($i < $num) {
    $jahr = array_shift($dir_array);
    if (ereg("([0-9]{4})", $jahr)) {
        # Jahresverzeichnisse von OPUS einlesen
        chop($jahr);
	$dir = opendir("$volltext_pfad/$jahr");
	$fd2 = fopen("$volltext_pfad/opus-index/dir", "w");
	while ($subpath = readdir($dir))
	{
		// !== verwendet, um 0 von False zu unterscheiden
		if (strpos($subpath, ".") !== 0) 
			fwrite($fd2, $subpath ."\n");
	}
	fclose($fd2);
	closedir($dir);
        $dir = fopen("$volltext_pfad/opus-index/dir", "r");
        while (!feof($dir)) {
            $source_opus = fgets($dir, 40986);
            $source_opus = rtrim($source_opus);
            if ($source_opus != "") {
                $anz++;
                if ($anz > 500) {
                    $anz = 1;
                    fwrite($fd, "</body>\n</html>\n");
                    fclose($fd);
                    $seite++;
                    $fd = fopen("$volltext_pfad/opus-index/opus-indexliste-$seite.html", "w");
                    fwrite($fd, "<html>\n<head>\n");
                    fwrite($fd, "<title>Liste aller Indexdateien der Volltexte von $projekt</title>\n</head>\n<body>\n");
                    fwrite($fd, "<h3>Liste aller Indexdateien der Volltexte von $projekt</h3>\n");
                    fwrite($fd, "Diese Seite ist für die Indexierung durch Suchmaschinen gedacht.<br>\n");
                }
                fwrite($fd, "<a href=\"$volltext_url/$jahr/$source_opus/index.html\">$volltext_url/$jahr/$source_opus/index.html</a><br> \n");
            }
        }
        fclose($dir);
    }
    $i++;
}
fwrite($fd, "</body>\n</html>\n");
fclose($fd);
$fd = fopen("$volltext_pfad/opus-index/opus-indexliste.html", "w");
fwrite($fd, "<html>\n<head>\n");
fwrite($fd, "<title>Liste aller Indexdateien der Volltexte von $projekt</title>\n</head>\n<body>\n");
fwrite($fd, "<h3>Liste aller Indexdateien der Volltexte von $projekt</h3>\n");
fwrite($fd, "Diese Seite ist für die Indexierung durch Suchmaschinen gedacht.<br>\n");
for ($i = 1;$i <= $seite;$i++) {
    fwrite($fd, "<a href='./opus-indexliste-$i.html'>Seite $i</a><br>\n");
}
fwrite($fd, "</body>\n</html>\n");
fclose($fd);
?>
