<?php
/**
 * E-Mail an den Autor eines Dokuments schreiben
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
 * @author      Pascal-Nicolas Becker <becker@zib.de>
 * @author      Stefan Freudenberg <stefan.freudenberg@bsz-bw.de>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id$
 */

require '../../lib/opus.class.php';
require '../../lib/stringValidation.php';

$opus = new OPUS('../../lib/opus.conf');
$php = $opus->value('php');
$opus_table = $opus->value('opus_table');
$temp_table = $opus->value('temp_table');
$url = $opus->value('url');
$projekt = $opus->value('projekt');
$titel = 'E-Mail an Autor senden';
$ueberschrift = $titel;

$from = $opus->value('email');
if (!preg_match('/^[\w.+-]{2,64}\@[\w.-]{2,255}\.[a-z]{2,6}$/', $from)) {
        die('Fehler in der Absendeadresse: ' . $from);
}

$mailto = $_REQUEST['verification'];
if (!preg_match('/^[\w.+-]{2,64}\@[\w.-]{2,255}\.[a-z]{2,6}$/', $mailto)) {
    die('Fehler in der Empfängeradresse: ' . $mailto);
}

$source_opus = (int)$_REQUEST['source_opus'];
if ($source_opus == 0) {
    die('Fehler in OPUS-ID: ' . $source_opus);
}

$frontdoor_url = "$url/frontdoor.php?source_opus=$source_opus"; 
$subject = "$projekt: Ihr Dokument wurde freigegeben.";
$message = "Sehr geehrte Autorin, sehr geehrter Autor!\n\n"
         . "Sie haben ein Dokument in $projekt eingegeben, dass nun " 
         . "veröffentlicht wurde.\n"
         . "Die Frontdoor zu Ihrem Dokument finden Sie unter "
		 . "$frontdoor_url.\n\n"
         . "Mit freundlichen Grüßen,\n"
         . "\n  Ihr $projekt-Team\n"
         . "--\n$url\n";
        
# A.Maile 10.2.05: Sprache einlesen aus opus.conf, falls nicht gesetzt
if (!$la) {
    $la = $opus->value('la');
}
# Annette Maile, 20.4.05 Design aus lib/design.php einlesen
require ("../../lib/design.$php");
$design = new design;
$design->head_titel($titel);
$design->head_ueberschrift($ueberschrift, $la);

// Soll die E-Mail versand werden?
if (isset($_REQUEST['senden'])) {
    // Erzeugen des Headers:
    $header = 'From: ' . $from . "\n" 
    		. 'BCC: ' . $from . "\n"
            . "Content-Type: text/plain; charset=ISO-8859-15;\n";
    // Versenden der Mail:
    if (mail($mailto, $subject, $message, $header)) {
        // Bestätigung ausgeben
        echo '<p>Nachricht von ' . $from . ' an ' . $mailto . 
             ' gesendet!<br />' . $message . '</p>';
    } else {
        // Unbekannter Fehler
        echo '<p>Die Nachricht konnte nicht gesendet werden! Bitte ' .
             'versenden Sie die E-Mail über ein normales E-Mailprogramm an ' .
             '<a href="mailto:' . $mailto . '">' . $mailto . '</a></p>';
    }
}

$design->foot("mail_autor.$php", $la);
?>
