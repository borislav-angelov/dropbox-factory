<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DropboxClient class main file
 *
 * PHP version 5
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  FileSystem
 * @package   DropboxFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/dropbox-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.0.0
 * @link      https://github.com/borislav-angelov/dropbox-factory/
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DropboxCurl.php';

/**
 * DropboxClient Main class
 *
 * @category  FileSystem
 * @package   DropboxFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/dropbox-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.0.0
 * @link      https://github.com/borislav-angelov/dropbox-factory/
 */
class DropboxClient
{
    // API Endpoints
    const API_URL         = 'https://api.dropbox.com/1/';
    const API_CONTENT_URL = 'https://api-content.dropbox.com/1/';

    /**
     * API Client
     * @var DropboxCurl
     */
    protected $api = null;

    /**
     * API Content Client
     * @var DropboxCurl
     */
    protected $content = null;

    public function __construct($accessToken) {
        $this->api = new DropboxCurl(self::API_URL, $accessToken);
        $this->content = new DropboxCurl(self::API_CONTENT_URL, $accessToken);
    }

    /**
     * Downloads a file from Dropbox
     *
     * @param  string   $path      The path to the file on Dropbox (UTF-8).
     * @param  resource $outStream If the file exists, the file contents will be written to this stream.
     * @return mixed
     */
    public function getFile($path, $outStream) {
        $this->content->setPath($path);
        $this->content->setOption(CURLOPT_WRITEFUNCTION, function($ch, $data) use ($outStream) {
            fwrite($outStream, $data);
        });

        return $this->content->makeRequest();
    }

    /**
     * Creates a folder
     *
     * @param  string $path The Dropbox path at which to create the folder (UTF-8).
     * @return mixed
     */
    public function createFolder($path){
        $this->api->setPath('/fileops/create_folder');
        $this->api->setOption(CURLOPT_POST, true);
        $this->api->setOption(CURLOPT_POSTFIELDS, array(
            'root' => 'auto',
            'path' => $path,
        ));

        return $this->api->makeRequest();
    }

    /**
     * Deletes a file or folder
     *
     * @param  string $path The Dropbox path of the file or folder to delete (UTF-8).
     * @return mixed
     */
    public function delete($path) {
        $this->api->setPath('/fileops/delete');
        $this->api->setOption(CURLOPT_POSTFIELDS, array(
            'root' => 'auto',
            'path' => $path,
        ));

        return $this->api->makeRequest();
    }

}
