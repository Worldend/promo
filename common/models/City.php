<?php

namespace common\models;

use Yii;
use common\models\scopes\CityQuery;

/**
 * This is the model class for table "cities".
 *
 * @property integer $id
 * @property integer $regions_id
 * @property string $domain
 * @property integer $active
 * @property string $meta_google
 * @property string $meta_yandex
 * @property string $longitude
 * @property string $latitude
 *
 * @property Ads[] $ads
 * @property Region $regions
 * @property CityText[] $cityTexts
 * @property Users[] $users
 */
class City extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['regions_id', 'domain'], 'required'],
            [['regions_id', 'active'], 'integer'],
            [['domain', 'meta_google', 'meta_yandex'], 'string', 'max' => 255],
            [['longitude', 'latitude'], 'string', 'max' => 100],
            [['regions_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['regions_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'regions_id' => 'Regions ID',
            'domain' => 'Domain',
            'active' => 'Active',
            'meta_google' => 'Meta Google',
            'meta_yandex' => 'Meta Yandex',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \backend\behaviors\SaveRelation::className(),
                'relations' => ['cityText']
            ],
            [
                'class' => \frontend\behaviors\Multilanguage::className(),
                'relationName' => 'cityText',
                'relationClassName' => CityText::className(),
            ],
        ];
    }

    /**
     * Виртуальный поля для набора объектов, полученный через asArray()
     */
//    public static function virtFields(){
//        return [
//            // Текущий url учитывая город
//            'url'=> function($model) {
//                return self::getUrl($model['domain']);
//            }
//        ];
//    }

    public static function find(){
        return new CityQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAds()
    {
        return $this->hasMany(Ads::className(), ['cities_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'regions_id'])
                    ->with('regionText');
    }

    public function getCityText()
    {
        return $this->hasOne(CityText::className(), ['cities_id' => 'id']);
//                    ->andWhere(['cities_text.languages_id' => Yii::$app->user->getLanguage()->id]);
    }

    public function getCityTexts()
    {
        return $this->hasMany(CityText::className(), ['cities_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['cities_id' => 'id']);
    }

    public function getHref(){
        return \yii\helpers\Url::toRoute($route);
    }

    /**
     * Вернуть массив данных городов для react компонента
     */
    public static function getComponentData($cities,$url){
        $normalize = function ($city) use ($url){
            return [
                    'id'   => $city->id,
                    'name' => $city->_text->name,
                    'url'  => \yii\helpers\Url::toRoute([
                        'city/generate-url',
                        'cityDomain' => $city['domain'],
                        'url' => $url]),
                    'region' => [
                        'id'   => $city->region->id,
                        'name' => $city->region->_text->name
                    ]
                ];
        };

        $url = $url ? $url : \Yii::$app->request->url;

        $data = [];

        if (!is_array($cities)){
            return $normalize($cities);
        }

        foreach ($cities as $city){
            $data = array_merge([$normalize($city)],$data);
        }

        return $data;
    }
}
