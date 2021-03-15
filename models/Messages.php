<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property int $id
 * @property string $destination
 * @property string $message
 * @property string $when_send
 * @property string $status_desc
 * @property string|null $creat
 */
class Messages extends \yii\db\ActiveRecord
{
    public const STATUS_WAITING = 'IN WAITING';
    public const STATUS_SENDING = 'SENDING';
    public const STATUS_SENT = 'SENT';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_ERROR = 'ERROR';
    public const STATUS_RECEIVED = 'RECEIVED';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['destination', 'message', 'status_desc', 'when_send'], 'required'],
            [['message'], 'string'],
            [['creat'], 'safe'],
            [['destination', 'status_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'destination' => 'Destination',
            'message' => 'Message',
            'status_desc' => 'Status Desc',
            'when_send' => 'When Send',
            'creat' => 'Creat',
        ];
    }

    /**
     * {@inheritdoc}
     * @return MessagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessagesQuery(get_called_class());
    }


    public static function sendMailReceviedMessage($message) {

        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['alertReceivedMessages'])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject('Ati primit un mesaj de la ' . $message->destination)
                ->setTextBody(json_encode($message->attributes))
                ->send();

            return true;
        }

        return false;



    }
}
