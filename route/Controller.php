<?php
namespace L\route;
use Exception;

class Controller{

	public $layout = 'main';

	public function __construct(){
		$this->init();
	}

	public function init(){

	}

    /**
     * render template
     * @param null $template
     * @param array $args
     * @return NULL
     */
    public function render($template, $args = []){
		$template = $this->parseTemplate($template);
        foreach($args as $k => $v){
            $$k = $v;
        }
        ob_start();
		include(APP.'view/'.$template);
		$content = ob_get_contents();
		ob_end_clean();
        include(APP.'view/layout/'.$this->layout.'.php');
	}

    /**
     * render partial template
     * @param $template
     * @param array $args
     * @return NULL
     */
    public function renderPartial($template, $args = []){
        $template = $this->parseTemplate($template);
        foreach($args as $k => $v){
            $$k = $v;
        }
        include(APP.'view/'.$template);
    }

	/**
	 * parse template place
	 * @template template information
     * @return string template position
	 */
	public function parseTemplate($template){
        if(strpos($template, '/') !== false){
            $template = ltrim($template,'/');
            $template = $template.'.php';
        }else{
            $template_info = get_class($this);
            $folders  = explode('\\', $template_info);
            $folder = str_replace('controller', '', strtolower(end($folders)));
            $template = $folder.'/'.$template.'.php';
        }
		return $template;
	}


}