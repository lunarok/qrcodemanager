<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

try {
  require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
  include_file('core', 'authentification', 'php');

  if (!isConnect('admin')) {
    throw new Exception(__('401 - Accès non autorisé', __FILE__));
  }

  if (init('action') == 'imgUpload') {
    if (!isset($_FILES['file'])) {
      throw new Exception(__('Aucun fichier trouvé. Vérifié parametre PHP (post size limit)', __FILE__));
    }
    $extension = strtolower(strrchr($_FILES['file']['name'], '.'));
    if (!in_array($extension, array('.jpg','.jpeg','.png'))) {
      throw new Exception('Extension du fichier non valide (autorisé .mp3 .png) : ' . $extension);
    }
    if (filesize($_FILES['file']['tmp_name']) > 10000000) {
      throw new Exception(__('Le fichier est trop gros (maximum 10000ko)', __FILE__));
    }
    if (!file_exists('/tmp/' . $_FILES['file']['name'])) {
      throw new Exception(__('Impossible d\'uploader le fichier (limite du serveur web ?)', __FILE__));
    }
    if (!move_uploaded_file($_FILES['file']['tmp_name'], '/tmp/' . init('id') . '.png') {
      throw new Exception(__('Impossible de déplacer le fichier temporaire', __FILE__));
    }
    $qrcode = qrcodemanager::byId(init('id'));
    $qrcode->scanImage();
    ajax::success();
  }

  throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
  /*     * *********Catch exeption*************** */
} catch (Exception $e) {
  ajax::error(displayExeption($e), $e->getCode());
}
?>