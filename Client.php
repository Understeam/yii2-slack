<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\slack;

use GuzzleHttp\Post\PostBody;
use understeam\httpclient\Event;
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

    /** @var string|\understeam\httpclient\Client */
    public $httpclient = 'httpclient';

    public function init()
    {
        if (is_string($this->httpclient)) {
            $this->httpclient = Yii::$app->get($this->httpclient);
        } elseif (is_array($this->httpclient)) {
            if (!isset($this->httpclient['class'])) {
                $this->httpclient['class'] = \understeam\httpclient\Client::className();
            }
            $this->httpclient = Yii::createObject($this->httpclient);
        }
        if (!$this->httpclient instanceof \understeam\httpclient\Client) {
            throw new InvalidConfigException("Client::httpclient must be either a Http client instance or the application component ID of a Http client.");
        }
    }

    public function send($text = null, $icon = null, $attachments = [])
    {
        $self = $this;
        $this->httpclient->request($this->url, 'POST', function (Event $event) use ($self, $text, $icon, $attachments) {
            $request = $event->message;
            /** @var \GuzzleHttp\Message\Request $request */
            $body = new PostBody();
            $body->setField('payload', Json::encode($self->getPayload($text, $icon, $attachments)));
            $request->setBody($body);
        });
    }

    protected function getPayload($text = null, $icon = null, $attachments = [])
    {
        if ($text === null) {
            $text = 'Yii message from ' . Yii::$app->id;
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
