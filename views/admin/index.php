<?php

use humhub\modules\darkMode\Module;
use humhub\modules\darkMode\models\Config;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Html;

$baseTheme = Yii::$app->view->theme->name;
if (!empty(Module::getThemeCombinations()[$baseTheme])) {
    $recommandation = Yii::t('DarkModeModule.admin', 'Recommended dark theme: ') . Module::getThemeCombinations()[$baseTheme];
} else {
    $recommandation = Yii::t('DarkModeModule.admin', 'Unfortunately we don\'t have a recommended dark theme for your theme.');
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= \Yii::t('DarkModeModule.base', '<strong>Dark Mode</strong> module configuration') ?>
    </div>
    <div class="panel-body">
        <div class="alert alert-info">
        <p>
            <?= Yii::t('DarkModeModule.admin', 'Current base theme: ') . $baseTheme ?></br>
            <?= $recommandation ?>
        </p>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'configure-form']);?>

            <?= $form->field($model, 'theme')->dropdownList($model->getThemes());
            ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
