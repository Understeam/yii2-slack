<?php
/**
 * @link https://github.com/Unsersteam
 * @copyright Copyright (c) 2016 Anatoly Rugalev
 * @license http://choosealicense.com/licenses/mit/
 */

namespace understeam\slack;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Basic log message formatter with common fields
 * @author Anatoly Rugalev
 * @link https://github.com/Understeam/yii2-slack
 */
class LogFormatter extends Component
{

    /**
     * @var string|callable
     */
    public $emoji;

    /**
     * @var string|callable
     */
    public $text;

    /**
     * @var array[]|callable[]|string[] attachment fields to send as attachments
     */
    public $fields = [];

    /**
     * @param array $messages Log messages to format
     * @return array with slack client arguments: ['text', ['attachments'], 'emoji']
     */
    public function format(array &$messages)
    {
        return [$this->getText($messages), $this->getAttachments($messages), $this->getEmoji($messages)];
    }

    protected function getEmoji(array &$messages)
    {
        if (is_callable($this->emoji)) {
            return call_user_func($this->emoji, $messages);
        } else {
            return $this->emoji;
        }
    }

    protected function getText(array &$messages)
    {
        if (is_callable($this->text)) {
            return call_user_func($this->text, $messages);
        } else {
            return $this->text;
        }
    }

    protected function buildAttachment($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        if (!is_string($text)) {
            $text = var_export($text, true);
        }

        return [
            'fallback' => "This is a log attachment",
            'text' => $text,
            'color' => $this->getLevelColor($level),
            'fields' => ArrayHelper::merge([
                [
                    "title" => "Level",
                    "short" => true,
                    "value" => Logger::getLevelName($level),
                ],
                [
                    "title" => "Category",
                    "short" => true,
                    "value" => $category,
                ],
                [
                    "title" => "Date",
                    "short" => true,
                    "value" => date('Y-m-d H:i:s', $timestamp),
                ],
                [
                    "Title" => "Route",
                    "short" => true,
                    "value" => Yii::$app->requestedRoute,
                ],
                [
                    "Title" => "Params",
                    "short" => true,
                    "value" => var_export(Yii::$app->requestedParams, true),
                ],
            ], $this->buildAttachmentFields($message)),
        ];
    }

    protected function buildAttachmentFields($message)
    {
        $fields = [];
        foreach ($this->fields as $title => $config) {
            $short = true;
            if (is_array($config) && !is_callable($config)) {
                $value = isset($config['value']) ? $config['value'] : null;
                if (is_numeric($title)) {
                    $title = isset($config['title']) ? $config['title'] : null;
                }
                if (isset($config['short'])) {
                    $short = $config['short'];
                }
            } else {
                $value = $config;
            }
            if (is_callable($value)) {
                $value = call_user_func($value, $message);
            }
            $fields[] = [
                'title' => $title,
                'short' => $short,
                'value' => $value,
            ];
        }
        return $fields;
    }

    protected function getAttachments(array &$messages)
    {
        $attachments = [];
        foreach ($messages as $i => $message) {
            $attachments[] = $this->buildAttachment($message);
        }
        return $attachments;

    }

    protected function getLevelColor($level)
    {
        $colors = [
            Logger::LEVEL_ERROR => 'danger',
            Logger::LEVEL_WARNING => 'warning',
            Logger::LEVEL_INFO => 'good',
            Logger::LEVEL_PROFILE => 'good',
            Logger::LEVEL_TRACE => 'good',
        ];
        if (!isset($colors[$level])) {
            return 'good';
        }
        return $colors[$level];
    }
}
