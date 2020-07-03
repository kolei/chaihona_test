<?php

namespace app\models;

use yii\base\Model;

/**
 * Our data model extends yii\base\Model class so we can get easy to use and yet 
 * powerful Yii 2 validation mechanism.
 */
class JsonData extends Model
{
    /**
     * We plan to get two columns in our grid that can be filtered.
     * Add more if required. You don't have to add all of them.
     */
    public $id;
    public $name;
    public $description;
    public $price;
    public $weigth;
    public $calories;
    public $volume;
    public $image;

    /**
     * Here we can define validation rules for each filtered column.
     * See http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * for more information about validation.
     */
    public function rules()
    {
        return [
            [['name', 'description','price','weigth','calories','volume'], 'string'],
            // our columns are just simple string, nothing fancy
        ];
    }

    /**
     * In this example we keep this special property to know if columns should be 
     * filtered or not. See search() method below.
     */
    private $_filtered = false;

    /**
     * This method returns ArrayDataProvider.
     * Filtered and sorted if required.
     */
    public function search($params)
    {
        /**
         * $params is the array of GET parameters passed in the actionExample().
         * These are being loaded and validated.
         * If validation is successful _filtered property is set to true to prepare
         * data source. If not - data source is displayed without any filtering.
         */
        if ($this->load($params) && $this->validate()) {
            $this->_filtered = true;
        }

        return new \yii\data\ArrayDataProvider([
            // ArrayDataProvider here takes the actual data source
            'allModels' => $this->getData(),
            'sort' => [
                // we want our columns to be sortable:
                'attributes' => ['name', 'id','price','weigth','calories','volume'],
            ],
        ]);
    }

    /**
     * Here we are preparing the data source and applying the filters
     * if _filtered property is set to true.
     */
    protected function getData()
    {
        $data = json_decode(\file_get_contents(\Yii::$app->basePath.'/web/uploads/data.json'), true)['products'];
        foreach ($data as $key => $value) {
            $data[$key]['weigth'] = $value['properties']['weigth'];
            $data[$key]['volume'] = $value['properties']['volume'];
            $data[$key]['calories'] = $value['properties']['calories'];
        }

        if ($this->_filtered) {
            $data = array_filter($data, function ($value) {
                $conditions = [true];
                if (!empty($this->name)) 
                    $conditions[] = mb_stripos($value['name'], $this->name) !== false;
                if (!empty($this->description)) 
                    $conditions[] = mb_stripos($value['description'], $this->description) !== false;
                if (!empty($this->price)) 
                    $conditions[] = mb_stripos($value['price'], $this->price) !== false;
                if (!empty($this->weigth)) 
                    $conditions[] = mb_stripos($value['weigth'], $this->weigth) !== false;
                if (!empty($this->calories)) 
                    $conditions[] = mb_stripos($value['calories'], $this->calories) !== false;
                return array_product($conditions);
            });
        }

        return $data;
    }
}