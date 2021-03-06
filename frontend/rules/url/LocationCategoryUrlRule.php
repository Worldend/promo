<?php
namespace frontend\rules\url;

use yii;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\web\UrlRule;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class LocationCategoryUrlRule extends UrlRule implements UrlRuleInterface
{

    public $categoryRoute = 'categories/index';

    public $categoryOnlyRoute = 'categories/only';

    public function createUrl($manager, $route, $params) {
        $url = '';

        if ($route === $this->categoryRoute) {

            $params = array_replace(Yii::$app->request->get(), $params);

            $placementSection = ArrayHelper::getValue($params, 'placement', false);
            $categorySection = ArrayHelper::getValue($params, 'category', false);

            if ($placementSection) { $placementSection = "$placementSection/"; }

            if ($categorySection) { $categorySection = "$categorySection/"; }

            $url = "{$placementSection}{$categorySection}";

            return $this->normalizeUrlForLocation($url);
        }

        if ($route === $this->categoryOnlyRoute){
            $categorySection = ArrayHelper::getValue($params, 'category', false);

            return $this->normalizeUrlForLocation($categorySection);
        }

        return false;;

    }

    protected function normalizeUrlForLocation($url){
        if (substr($url, -1) !== '/') {
            $url = "$url/";
        }

        if (Yii::$app->location->city){
            $url = $url . Yii::$app->location->city->domain;
        } else {
            $url = $url;
        }

        return trim($url, '/');
    }

    protected function setParams($params){
       $urlSections = Yii::$app->request->get();
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $result = parent::parseRequest($manager, $request);

        if ($result === false) {
            return false;
        }

        list($route, $params) = $result;

        if (isset($params['placement']) && !$this->isValidPlacement($params)) {
            return false;
        }

        if (! $this->isValidCategory($params)) return false;

        if ($params['city'] && !$this->isValidCity($params)){
            return false;
        }

        return [$route,$params];
    }

    public function isValidPlacement($params){
        $placementName = ArrayHelper::getValue($params, 'placement', false);

        $placement = \common\models\Placement::find()
                        ->seoUrl($placementName)
                        ->one();

        if ($placement) {
            \common\models\Placement::setCurrent($placement);
        }

        return $placement;
    }

    private function isValidCategory($params){
        $categoryName = ArrayHelper::getValue($params, 'category', false);

        $category = \common\models\Category::find()
                            ->searchUrlByLanguage($categoryName)
                            ->one();
        return $category;
    }

    /**
     * @return bool or \common\models\City $object
     */
    private function isValidCity($params){

        $cityName = ArrayHelper::getValue($params, 'city', false);

        $city = \common\models\City::find()
                        ->byLocation()
                        ->whereDomain($cityName)
                        ->one();

        Yii::$app->location->city = $city;

        return \common\models\City::find()
                        ->byLocation()
                        ->whereDomain($cityName)
                        ->one();
    }
}
