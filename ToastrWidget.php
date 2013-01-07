<?php
/**
 * ToastrWidget class file.
 *
 * Wrapper for http://codeseven.github.com/toastr/
 * This jquery.toastr.{js,css} is modified to add animate.css bounceInLeft efect http://daneden.me/animate/
 * Versão 1.0.2
 * 
 * @author Tonin R. Bolzan <admin@tonybolzan.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 0.2
 *
 * @see
 */
class ToastrWidget extends CWidget {

    public $options = array();
    public $autoRegister = true;

    /**
     * Renders the widget.
     */
    public function run() {
        $alert = array();
        
        if($this->options) {
            $json = CJSON::encode($this->options);
            $alert[] = "toastr.options = $json;";
        }
        
        foreach (Yii::app()->user->getFlashes() as $key => $message) {
            $alert[] = "toastr['{$key}']('{$message}');";
        }

        $this->registerScripts(__CLASS__ . '#' . $this->id, implode(' ', $alert));
    }

    protected function registerScripts($id, $embeddedScript) {
        $cs = Yii::app()->clientScript;

        if ($this->autoRegister) {
            $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
            $baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1, YII_DEBUG);
            $scriptFile = YII_DEBUG ? '/jquery.toastr.js' : '/jquery.toastr.min.js';
            $cssFile = YII_DEBUG ? '/jquery.toastr.css' : '/jquery.toastr.min.css';

            $cs->registerCoreScript('jquery');
            $cs->registerScriptFile($baseUrl . $scriptFile);
            $cs->registerCssFile($baseUrl . $cssFile);
        }

        $cs->registerScript($id, $embeddedScript, CClientScript::POS_LOAD);
    }

}