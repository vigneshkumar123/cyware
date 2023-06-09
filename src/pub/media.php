<?php

class Media {

    private $mediaDir;

    protected $mimeTypes = array(

        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

        'woff'=> 'application/x-font-woff'
    );

    /**
     * Media constructor.
     * @param $mediaDir
     */
    public function __construct($mediaDir)
    {
        $this->mediaDir = $mediaDir;
    }

    public function __invoke()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        preg_match('/^\/media\/(.*)$/', $requestUri, $matches);
        $resource = __DIR__ . '/media/' . $matches[1];

        if ($resource && file_exists($resource)) {
            $extension = pathinfo($resource, PATHINFO_EXTENSION);
            $mimeType = array_key_exists($extension, $this->mimeTypes) ? $this->mimeTypes[$extension] : $this->mimeTypes['txt'];
            header("Content-type: $mimeType; charset: UTF-8");
            readfile($resource);
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }
}
