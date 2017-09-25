<?php

namespace console\controllers;

class MyController extends \yii\console\Controller
{
    public function actionIndex()
    {
        //system('pwd', $error);
        system("g++ ../web/code/test.cpp -o ../web/code/test.out", $error);
        //system("!#/bin/bash ./frontend/web/code/test.out <<< 55", $error);
        echo $error;
    }
}