<?php

namespace humhub\modules\darkMode\models;

use humhub\modules\ui\view\helpers\ThemeHelper;
use Yii;

/**
 * Module Configuration model
 */
class Config extends \yii\base\Model
{
    const DARK_CSS_SUFFIX = ' (dark)';
    const FALLBACK = 'DarkHumHub';
    
    public $theme;
    
    public function init()
    {
        parent::init();

        $settings = Yii::$app->getModule('dark-mode')->settings;

        $this->theme = $settings->get('theme');
        
        // If no setting was found, get recommended theme or fallback (DarkHumHub) 
        if (empty($this->theme)) {
            $this->theme = self::getRecommendedTheme();
            if (empty($this->theme)) {
                $this->theme = self::FALLBACK;
            }
        }
    }

    public function rules()
    {
        return [
            ['theme', 'in', 'range' => array_keys($this->getThemes())],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'theme' => Yii::t('DarkModeModule.admin', 'Dark Theme')
        ];
    }
    
    public function attributeHints()
    {
        return [
            'theme' => Yii::t('DarkModeModule.admin', 'The stylesheet of the selected theme will be used for the dark mode.')
        ];
    }
    
    public function getThemes()
    {
        $themes = [];
        
        foreach (ThemeHelper::getThemes() as $theme) {
            // Themes with a dark.css
            if (file_exists($theme->basePath . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'dark.css')) {
                $themes[$theme->name . self::DARK_CSS_SUFFIX] = $theme->name . self::DARK_CSS_SUFFIX;
            // Themes containing "dark" in their name
            } elseif (stripos($theme->name, 'dark') !== false) {
                $themes[$theme->name] = $theme->name;
            }
        }
        
        // Add "HumHub (dark)"
        $themes['DarkHumHub'] = 'HumHub' . self::DARK_CSS_SUFFIX;
        
        // Add "enterprise (dark)" if module enabled
        $enterprise = 'enterprise-theme';
        if (Yii::$app->hasModule($enterprise) && isset(Yii::$app->modules[$enterprise])) {
            $themes['DarkEnterprise'] = 'enterprise' . self::DARK_CSS_SUFFIX;
        }
        
        return $themes;
    }
    
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $settings = Yii::$app->getModule('dark-mode')->settings;
        $settings->set('theme', $this->theme);

        return true;
    }
    
    /*
     * returns array path and file name of stylesheet
     * used for the assets
     */
    public function getThemeInfos()
    {
        $info['fileName'] = 'theme.css';
        
        if ($this->theme == 'DarkEnterprise') {
            $info['path'] = '@dark-mode' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'DarkEnterprise' . DIRECTORY_SEPARATOR . 'css';
        } elseif ($this->theme == 'DarkHumHub') {
            $info['path'] = '@dark-mode' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'DarkHumHub' . DIRECTORY_SEPARATOR . 'css';
        } else {
            // Themes with dark.css
            if (strpos($this->theme, self::DARK_CSS_SUFFIX) !== false) {
                $this->theme = str_replace(self::DARK_CSS_SUFFIX, '', $this->theme);
                $info['fileName'] = 'dark.css';
            }
            $info['path'] = ThemeHelper::getThemeByName($this->theme)->basePath . DIRECTORY_SEPARATOR . 'css';
        }
        return $info;
    }
    
    // Try to return recommended theme
    public function getRecommendedTheme()
    {
        $baseTheme = Yii::$app->view->theme->name;
        
        if ($baseTheme == 'HumHub') {
            return 'DarkHumHub';
        } elseif ($baseTheme == 'enterprise') {
            return 'DarkEnterprise';
        } else {
            $basePath = ThemeHelper::getThemeByName($baseTheme)->basePath; 
            if (file_exists($basePath . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'dark.css')) {
                return $baseTheme . self::DARK_CSS_SUFFIX;
            }
        }
        return '';
    }
}