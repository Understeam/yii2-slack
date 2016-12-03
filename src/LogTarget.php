<?php
/**
 * @link https://github.com/Unsersteam
 * @copyright Copyright (c) 2016 Anatoly Rugalev
 * @license http://choosealicense.com/licenses/mit/
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

    /**
     * @var string used to send messages to non-default channel
     */
    public $channel;
    /**
     * @var \understeam\slack\Client|string
     */
    public $slack = 'slack';

    /**
     * @var array|LogFormatter
     */
    public $formatter = [
        'class' => 'understeam\slack\WebFormatter'
    ];

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

        if (is_array($this->formatter) && !isset($this->formatter['class'])) {
            $this->formatter['class'] = WebFormatter::className();
        }
        $this->formatter = Yii::createObject($this->formatter);
        if (!$this->formatter instanceof LogFormatter) {
            throw new InvalidConfigException("LogTarget::formatter must be LogFormatter instance.");
        }
        parent::init();
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        list($message, $attachments, $emoji) = $this->formatter->format($this->messages);
        $this->slack->send($message, $attachments, $emoji, $this->channel);
    }
}
