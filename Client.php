<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\slack;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

/**
 * Class Client TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class Client extends Component
{
    public $url;
    public $username;
    public $emoji;
    public $defaultText = "Message from Yii application";

    /** @var string|\yii\httpclient\Client */
    public $httpclient = 'httpclient';

    public function init()
    {
        if (is_string($this->httpclient)) {
            $this->httpclient = Yii::$app->get($this->httpclient);
        } elseif (is_array($this->httpclient)) {
            if (!isset($this->httpclient['class'])) {
                $this->httpclient['class'] = 'yii\httpclient\Client';
            }
            $this->httpclient = Yii::createObject($this->httpclient);
        }
        if (!$this->httpclient instanceof \yii\httpclient\Client) {
            throw new InvalidConfigException("Client::httpclient must be either a Http client instance or the application component ID of a Http client.");
        }
    }

    public function send($text = null, $icon = null, $attachments = [])
    {
        $this->httpclient->post($this->url, [
            'payload' => Json::encode($this->getPayload($text, $icon, $attachments)),
        ])->send();
    }

    protected function getPayload($text = null, $icon = null, $attachments = [])
    {
        if ($text === null) {
            $text = $this->defaultText;
        }

        $payload = [
            'text' => $text,
            'username' => $this->username,
            'attachments' => $attachments,
        ];
        if ($icon !== null) {
            $payload['icon_emoji'] = $icon;
        }
        return $payload;
    }

}
