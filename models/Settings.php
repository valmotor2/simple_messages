<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $desc
 * @property string|null $options
 * @property string|null $creat
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['desc'], 'required'],
            [['options', 'creat'], 'safe'],
            [['desc'], 'string', 'max' => 255],
            [['desc'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'desc' => 'Desc',
            'options' => 'Options',
            'creat' => 'Creat',
        ];
    }

    /**
     * {@inheritdoc}
     * @return SettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingsQuery(get_called_class());
    }


    static public function optionsMessages()
    {
        $messages = Settings::find()->where(['desc' => 'messages'])->one();

        return json_decode($messages->options);
    }
}
