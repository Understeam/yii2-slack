<?php
/**
 * @link https://github.com/Unsersteam
 * @copyright Copyright (c) 2016 Anatoly Rugalev
 * @license http://choosealicense.com/licenses/mit/
 */

namespace understeam\slack;

/**
 * Log message formatter to use with Integram webhook bot (Horn)
 * @author Anatoly Rugalev
 * @link https://github.com/Understeam/yii2-slack
 */
class HornFormatter extends LogFormatter
{

    protected function getText(array &$messages)
    {
        // TODO: fix formatting
        $text = "";
        $title = parent::getText($messages);
        if (!$title) {
            $title = 'New log message';
        }
        $text .= '*' . $title . '*';
        $text .= "\n";
        $attachments = parent::getAttachments($messages);
        foreach ($attachments as $attachment) {
            if (isset($attachment['text'])) {
                $text .= "```text\n";
                $text .= $attachment['text'] . "\n";
                $text .= "```\n";
            }
            if (isset($attachment['fields']) && is_array($attachment['fields'])) {
                foreach ($attachment['fields'] as $field) {
                    if (isset($field['title'])) {
                        $text .= "*" . $field['title'] . "*: ";
                    }
                    if (isset($field['value'])) {
                        $text .= "`{$field['value']}`\n";
                    }
                }
            }
        }
        $text = strtr($text, [
            '->' => '::',
            ' => ' => ': ',
            '<' => '&lt;',
            '>' => '&gt;',
            '&' => '&amp;',
        ]);
        return $text;
    }

    protected function getAttachments(array &$messages)
    {
        return [];
    }
}