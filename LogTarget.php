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

    public function init() {
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
        $this->slack->send(null, $this->emoji, $this->getAttachments());
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
            $attachments[] = [
                'fallback' => 'Log message ' . ($i + 1),
                'text' => $message[0],
                'pretext' => $message[2],
                'color' => $this->getLevelColor($message[1]),
                'fields' => [
                    [
                        'title' => 'Level',
                        'value' => $message[1],
                        'short' => true,
                    ],
                    [
                        'title' => 'Category',
                        'value' => $message[2],
                        'short' => true,
                    ],
                    [
                        'title' => 'Timestamp',
                        'value' => $message[3],
                        'short' => true,
                    ],
                    [
                        'title' => 'Referrer',
                        'value' => Yii::$app->has('request') && Yii::$app->request instanceof Request ? Yii::$app->request->referrer : 'unknown',
                        'short' => true,
                    ],
                    [
                        'title' => 'User IP',
                        'value' => Yii::$app->has('request') && Yii::$app->request instanceof Request ? Yii::$app->request->userIP : 'unknown',
                        'short' => true,
                    ],
                    [
                        'title' => 'URL',
                        'value' => Yii::$app->has('request') && Yii::$app->request instanceof Request ? Yii::$app->request->url : 'unknown',
                        'short' => true,
                    ],
                ],
            ];
        }
        return $attachments;
    }
}
