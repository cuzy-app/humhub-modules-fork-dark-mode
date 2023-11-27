<?php

namespace humhub\modules\darkMode\controllers;

use humhub\modules\darkMode\models\UserSetting;
use humhub\widgets\ModalClose;
use Yii;

class UserController extends \humhub\components\Controller
{
    public function actionIndex()
    {
        $form = new UserSetting();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {

            // Close modal and reload page to make apply asset changes (this way the "Saved" message is not shown)
            return ModalClose::widget(['saved' => true]) .
                ' <script ' . \humhub\libs\Html::nonce() . '>$(function () { location.reload() }); </script>';
        }

        return $this->renderAjax('index', ['model' => $form]);
    }
}
