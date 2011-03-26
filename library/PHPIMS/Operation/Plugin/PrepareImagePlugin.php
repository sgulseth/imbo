<?php
/**
 * PHPIMS
 *
 * Copyright (c) 2011 Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package PHPIMS
 * @subpackage OperationPlugin
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/phpims
 */

/**
 * Prepare image plugin
 *
 * This plugin will kick in before the AddImage operation executes. The plugin will prepare the
 * image object so it can be added using a database and a storage driver,
 *
 * @package PHPIMS
 * @subpackage OperationPlugin
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/phpims
 */
class PHPIMS_Operation_Plugin_PrepareImage extends PHPIMS_Operation_Plugin_Abstract {
    /**
     * @see PHPIMS_Operation_Plugin_Abstract::preExec
     */
    public function preExec() {
        $image = $this->getOperation()->getImage();

        $imagePath = $_FILES['file']['tmp_name'];
        $info = getimagesize($imagePath);
        $extension = image_type_to_extension($info[2], false);
        $actualHash = md5_file($imagePath) . '.' . $extension;
        $hashFromRequest = $this->getOperation()->getHash();

        if ($actualHash !== $hashFromRequest) {
            throw new PHPIMS_Operation_Plugin_Exception('Hash mismatch', 400);
        }

        $image->setFilename($_FILES['file']['name'])
              ->setFilesize($_FILES['file']['size'])
              ->setMetadata($_POST)
              ->setHash($hashFromRequest)
              ->setMimeType($info['mime']);
    }
}