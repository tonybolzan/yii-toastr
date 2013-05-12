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
 *
 * Alternate usage example :
 *
 * $this->Widget('ext.yii-toastr.ToastrWidget',array(
 *	'options' => array(
 *		'positionClass' => 'toast-top-left animated bounceInLeft'
 *	),
 *	'encode' => false,
 *	'messages' => array(
 *		array(
 *			'type'   => 'warning',
 *			'message'=> 'this is a <warning> message',
 *			'encode' => true
 *		),
 *		array(
 *			'type'    => 'success',
 *			'message' => 'this is a success message',
 *			'title'   => '<u>this is the title</u>',
 *		),
 *		array(
 *			'type'    => 'error',
 *			'message' => 'this is an ERROR message',
 *		)
 *	)
 * ));
 */
class ToastrWidget extends CWidget {

	/**
	 * @var array Jquery Plugin options (see https://github.com/CodeSeven/toastr)
	 */
    public $options      = array();
    public $autoRegister = true;
    /**
     * @var boolean when TRUE, all messages and titles are HTML encoded
     */
    public $encode       = false;
    /**
     * @var array list of messages to display. Each item in this array is an array with 3 possible
     * keys :
     * <ul>
     *	<li>type    : (mandatory) one of 'success', 'info', 'error', 'warning'</li>
     *	<li>message : (optional) the message itself</li>
     *	<li>title   : (optional) the message title</li>
     *	<li>encode  : (optional) boolean defining if this item message and title should be HTML encoded or not. If this option is
     *	set, it overwrites the global 'encode' property</li>
     * </ul>
     */
    public $messages = array();

    /**
     * Renders the widget.
     */
    public function run() {
    	$alert = array();
    
    	if($this->options) {
    		$json = CJSON::encode($this->options);
    		$alert[] = "toastr.options = $json;";
    	}
    
    	if(count($this->messages) != 0){
    		foreach ($this->messages as $item) {

    			$encode = ( isset($item['encode'])
    				? (bool) $item['encode']
    				: $this->encode
    			);
    			
    			$title = ( isset($item['title'])
    				? $item['title']
    				: null
    			);
    			
    			$title  = ( $encode === true
    				? CHtml::encode($title)
    				: $title
    			);
    			
    			$msg = ( $encode === true
    				? CHtml::encode($item['message'])
    				: $item['message']
    			);
    			$alert[] = "toastr['{$item['type']}']('{$msg}','{$title}');";
    		}
    	}else {
    		// fallback into default behavior (with optional HTML encode)
	    	foreach (Yii::app()->user->getFlashes() as $key => $message) {
	    		
	    		$msg = ( $this->encode === true
	    			? CHtml::encode($message)
	    			: $message
	    		);
	    		
	    		$alert[] = "toastr['{$key}']('{$message}');";
	    	}
    	}
    
    	$this->registerScripts(__CLASS__ . '#' . $this->id, implode(' ', $alert));
    }

    /**
     * Register plugin assets if <em>autoRegister</em> is true and register the toastr
     * script.
     *
     * @param string $id
     * @param string $embeddedScript
     */
    protected function registerScripts($id, $embeddedScript) {
        $cs = Yii::app()->clientScript;

        if ($this->autoRegister) {
            $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
            $baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1, YII_DEBUG);
            $scriptFile = YII_DEBUG ? '/toastr.js' : '/toastr.min.js';
            $cssFile = YII_DEBUG ? '/toastr.css' : '/toastr.min.css';

            $cs->registerCoreScript('jquery');
            $cs->registerScriptFile($baseUrl . $scriptFile);
            $cs->registerCssFile($baseUrl . $cssFile);
        }

        $cs->registerScript($id, $embeddedScript, CClientScript::POS_LOAD);
    }

}