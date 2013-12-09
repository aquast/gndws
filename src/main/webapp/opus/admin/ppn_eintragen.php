<?php
/**
 * OPUS application file
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
 * @author      Stefan Freudenberg <stefan.freudenberg@bsz-bw.de>
 * @copyright   Universitaetsbibliothek Stuttgart, 1998-2007
 * @license     http://www.gnu.org/licenses/gpl.html
 * @version     $Id: ppn_eintragen.php 188 2007-04-25 11:31:29Z freudenberg $
 */

require_once '../../lib/opus.class.php';
require_once '../../lib/stringValidation.php';
require_once '../../lib/opusrecord.class.php';
require_once 'DB.php';

$opus = new OPUS('../../lib/opus.conf');
$dsn = $opus->value('dbms') . '://' . $opus->value('db_user') . ':' . $opus->value('db_passwd') . '@localhost/' . $opus->value('db');

define('OPUS_HOME', $opus->value('url'));
define('OPUS_TABLE', $opus->value('opus_table'));
define('OPUS_LANGUAGE', $opus->value('la'));

if (isset($_REQUEST['urn']) && isset($_REQUEST['ppn'])) {
    $connection = &DB::connect($dsn);
    if (PEAR::isError($connection)) {
        die($connection->getMessage());
    }
    $connection->setOption('autofree', true);
    $connection->setFetchMode(DB_FETCHMODE_ASSOC);

    $record = &OpusRecord::findByUrn($connection, $_REQUEST['urn']);
    if (PEAR::isError($record)) {
        die($record->getMessage());
    }
    $record->setSourceSwb($_REQUEST['ppn']);

    $result = &$record->update($connection);
    if (PEAR::isError($result)) {
        die($result->getMessage());
    } else {
        echo 'PPN erfolgreich eingetragen.';
    }
} else {
    echo 'Fehlende PPN oder URN';
}
?>
