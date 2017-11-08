<?php
namespace backend\compenents;

use yii\base\Component;

class User extends Component
{
    public function __construct(array $config)
    {
        Parent::__construct($config);
    }

    public function init(){
        Parent::className();
    }
}