<?php
include __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\RequestOptions;

class Whatsapp
{
    protected $instance_id = null;
    protected $clientId = null;
    protected $clientSecret = null;


    /**
     * Whatsapp constructor.
     * @param $instance_id
     * @param $clientId
     * @param $clientSecret
     */
    public function __construct($instance_id, $clientId, $clientSecret)
    {
        $this->instance_id = $instance_id;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    /**
     * @param $message
     * @param $groupAdmin
     * @param $groupName
     * @return bool|string
     */
    public function sendTextToGroup($groupAdmin, $groupName, $message)
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToGroup($groupAdmin, $groupName, strip_tags($message));
    }


    /**
     * @param $message
     * @param $number
     * @param $message
     * @return bool|string
     */
    public function sendTextToUser($number, $message)
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToUser($number, strip_tags($message));
    }


    /**
     * @param $fileInBase64
     * @param $groupAdmin
     * @param $groupName
     * @return bool|string
     */
    public function sendImageToGroup($groupAdmin, $groupName, $fileInBase64)
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToGroup($groupAdmin, $groupName, $fileInBase64, true);
    }


    /**
     * @param $number
     * @param $image
     * @param $caption
     * @return bool|string
     */
    public function sendImageToUser($number, $image, $caption)
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToUser($number, $image,true, false, $caption);
    }


    /**
     * @param $fileInBase64
     * @param string $fileName
     * @param $groupAdmin
     * @param $groupName
     * @return bool|string
     */
    public function sendDocumentToGroup($groupAdmin, $groupName, $fileInBase64, $fileName = 'doc.pdf')
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToGroup($groupAdmin, $groupName, $fileInBase64, false, true, $fileName);
    }

    /**
     * @param $number
     * @param $document
     * @param $filename
     * @return bool|string
     */
    public function sendDocumentToUser($number, $document, $filename)
    {
        if (!$this->instance_id) {
            return false;
        }
        return $this->sendWhatsAppToUser($number, $document,false, true, $filename);
    }


    /**
     * @param string $groupAdmin = Specify the WhatsApp number of the group creator, including the country code
     * @param string $groupName = Specify the name of the group
     * @param $textOrFile
     * @param bool $isImage
     * @param bool $isDocument
     * @param null $documentName
     * @return bool|string
     */
    private function sendWhatsAppToGroup(string $groupAdmin, string $groupName, $textOrFile, $isImage = false, $isDocument = false, $documentName = null)
    {
        if (!$this->instance_id) {
            return false;
        }

        $postData = [
            'group_admin' => $groupAdmin,
            'group_name' => $groupName,
        ];

        if ($isImage) {
            $postData['image'] = $textOrFile;
            $url = 'http://api.whatsmate.net/v3/whatsapp/group/image/message/' . $this->instance_id;
        } elseif ($isDocument) {
            $postData['document'] = $textOrFile;
            $postData['filename'] = $documentName;
            $url = 'http://api.whatsmate.net/v3/whatsapp/group/document/message/' . $this->instance_id;
        } else {
            $postData['message'] = $textOrFile;
            $url = 'http://api.whatsmate.net/v3/whatsapp/group/text/message/' . $this->instance_id;
        }

        $client = new \GuzzleHttp\Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-WM-CLIENT-ID' => $this->clientId,
                    'X-WM-CLIENT-SECRET' => $this->clientSecret
                ]
            ]
        );

        try {
            $res = $client->post($url,
                [
                    RequestOptions::JSON => $postData

                ]
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $res->getBody()->getContents();
    }

    private function sendWhatsAppToUser($number, $textOrFile, $isImage = false, $isDocument = false, $documentName = 'doc') {
        if (!$this->instance_id) {
            return false;
        }

        $postData = [
            'number' => $number,  // TODO: Specify the recipient's number here. NOT the gateway number
            'message' => $textOrFile
        ];

        if ($isImage) {
            $postData['image'] = $textOrFile;
            $postData['caption'] = $documentName;
            $url = 'http://api.whatsmate.net/v3/whatsapp/single/image/message/' . $this->instance_id;
        } elseif ($isDocument) {
            $postData['document'] = $textOrFile;
            $postData['filename'] = $documentName;
            $url = 'http://api.whatsmate.net/v3/whatsapp/single/document/message/' . $this->instance_id;
        } else {
            $postData['message'] = $textOrFile;
            $url = 'http://api.whatsmate.net/v3/whatsapp/single/text/message/' . $this->instance_id;
        }

        $client = new \GuzzleHttp\Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-WM-CLIENT-ID' => $this->clientId,
                    'X-WM-CLIENT-SECRET' => $this->clientSecret
                ]
            ]
        );

        try {
            $res = $client->post($url,
                [
                    RequestOptions::JSON => $postData

                ]
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $res->getBody()->getContents();
    }
}