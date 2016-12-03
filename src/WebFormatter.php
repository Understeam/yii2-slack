<?php
/**
 * @link https://github.com/Unsersteam
 * @copyright Copyright (c) 2016 Anatoly Rugalev
 * @license http://choosealicense.com/licenses/mit/
 */

namespace understeam\slack;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Request;

/**
 * Log message formatter to use for web requests logging
 * @author Anatoly Rugalev
 * @link https://github.com/Understeam/yii2-slack
 */
class WebFormatter extends LogFormatter
{

    public function buildAttachment($message)
    {
        $attachment = parent::buildAttachment($message);
        if (!Yii::$app->request instanceof Request) {
            return $attachment;
        }
        if (!isset($attachment['fields'])) {
            $attachment['fields'] = [];
        }
        $attachment['fields'] = ArrayHelper::merge($this->getAttachmentFields(), $attachment['fields']);
        return $attachment;
    }

    protected function getAttachmentFields()
    {
        return [
            'IP' => Yii::$app->request->getUserIP(),
            'URL' => Yii::$app->request->url,
            'Method' => Yii::$app->request->method,
        ];
    }
}
