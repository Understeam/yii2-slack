<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */
namespace understeam\slack;

use Yii;
use yii\base\InvalidConfigException;
use yii\log\Logger;
use yii\log\Target;
use yii\web\Request;

/**
 * Class LogTarget TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class LogTarget extends Target
{
    public $sendLogs = false;

    public $emoji = null;

    /**
     * @var \understeam\slack\Client|string
     */
    public $slack = 'slack';

    public function init()
    {
        if (is_string($this->slack)) {
            $this->slack = Yii::$app->get($this->slack);
        } elseif (is_array($this->slack)) {
            if (!isset($this->slack['class'])) {
                $this->slack['class'] = Client::className();
            }
            $this->slack = Yii::createObject($this->slack);
        }
        if (!$this->slack instanceof Client) {
            throw new InvalidConfigException("LogTarget::slack must be either a Slack client instance or the application component ID of a Slack client.");
        }
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $this->slack->send("Log message", $this->emoji, $this->getAttachments());
    }

    public function getLevelColor($level)
    {
        $colors = [
            Logger::LEVEL_ERROR => 'danger',
            Logger::LEVEL_WARNING => 'danger',
            Logger::LEVEL_INFO => 'good',
            Logger::LEVEL_PROFILE => 'warning',
            Logger::LEVEL_TRACE => 'warning',
        ];
        if (!isset($colors[$level])) {
            return 'good';
        }
        return $colors[$level];
    }

    public function getAttachments()
    {
        $attachments = [];
        foreach ($this->messages as $i => $message) {
            $attachment = [
                'fallback' => 'Log message ' . ($i + 1),
                'text' => $this->formatMessage($message),
                'pretext' => $message[0],
                'color' => $this->getLevelColor($message[1]),
                'fields' => [
                    [
                        'title' => 'Application ID',
                        'value' => Yii::$app->id,
                        'short' => true,
                    ],
                ],
            ];
            if (Yii::$app->has('request') && ($request = Yii::$app->request) instanceof Request) {
                $attachment['fields'][] = [
                    'title' => 'Referrer',
                    'value' => $request->getReferrer(),
                    'short' => true,
                ];
                $attachment['fields'][] = [
                    'title' => 'User IP',
                    'value' => $request->getUserIP(),
                    'short' => true,
                ];
                $attachment['fields'][] = [
                    'title' => 'URL',
                    'value' => $request->getAbsoluteUrl(),
                    'short' => true,
                ];
            }
            $attachments[] = $attachment;
        }
        return $attachments;
    }
}
