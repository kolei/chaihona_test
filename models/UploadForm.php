<?php

namespace app\models;

use Yii;
use yii\base\Model;

class UploadForm extends Model{
 
    public $file;

    private $categories = [];
 
    public function rules(){
        return [
            [['file'], 'file', 'extensions' => 'xlsx', 
                    'skipOnEmpty' => false]
        ];
    }

    private function getCategoryIndex($name){
        foreach ($this->categories as $key => $value)
            if($value['name']==$name) return $key;
        $this->categories[] = array(
            'name' => $name,
            'productIds' => []
        );
        return count($this->categories)-1;
    }

    private function addCategory($name, $id){
        $index = $this->getCategoryIndex($name);
        $this->categories[$index]['productIds'][] = $id;
    }

    public function upload() {
        if ($this->validate()) {

            $data = \moonland\phpexcel\Excel::import($this->file->tempName/*, $config*/); // $config is an optional

            $products = [];

            foreach ($data as $key => $row) {
                $row_values = \array_values($row);
                if( !empty($row_values[1]) ){
                    $this->addCategory($row_values[2], $key+1);

                    $properties = array(
                        'calories' => $row_values[0]
                    );

                    if(\preg_match('/(.*)\s*г/', $row_values[5], $matches)===0){
                        $properties['volume'] = $row_values[5];
                        $properties['weigth'] = null;
                    } else {
                        $properties['volume'] = null;
                        $properties['weigth'] = $matches[1];
                    }

                    $products[] = array(
                        'id' => $key+1,
                        'name' => $row_values[1],
                        'price' => $row_values[3],
                        'description' => $row_values[4],
                        'image' => $row_values[6],
                        'properties' => $properties
                    );
                }
            }

            file_put_contents('uploads/data.json', \json_encode(array(
                'categories' => $this->categories,
                'products' => $products
            ), JSON_UNESCAPED_UNICODE));

            //$this->file->saveAs('uploads/data.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }
}