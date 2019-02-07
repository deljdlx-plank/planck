<?php

namespace Planck\View;

use Phi\HTML\CSSFile;
use Phi\HTML\Document;
use Planck\Application;
use Planck\Exception;
use Planck\Traits\IsApplicationObject;
use Planck\Traits\HasLocalResource;


class Layout extends Document
{

    use IsApplicationObject;
    use HasLocalResource;

    /**
     * @var ComponentManager
     */
    protected $componentManager;


    /**
     * @var Application
     */
    protected $application;


    /**
     * @var ViewComponent[]
     */
    protected $components = [];


    public function __construct(Application $application = null)
    {
        parent::__construct();


        if($application) {
            $this->setApplication($application);
        }



        if($this->getApplication()->exists('view-component-manager')) {
            $this->componentManager = $this->getApplication()->get('view-component-manager');
        }
        else {

            $this->componentManager = $this->getDefaultComponentManager();
        }
    }


    public function addResourcesFromResponses($responses)
    {

        foreach ($responses as $response) {

            if($resources = $response->getExtraData('resources')) {
                foreach ($resources as $resource) {
                    if($resource instanceof \Phi\HTML\JavascriptFile) {

                        $this->addJavascriptFile($resource);
                    }
                    else if($resource instanceof CSSFile) {
                        $this->addCSSFile($resource);
                    }
                }
            }
        }
    }



    public function getComponent($componentName)
    {
        if(array_key_exists($componentName, $this->components)) {
            return $this->components[$componentName];
        }

        throw new Exception('Component '.$componentName.' is not registered');
    }

    public function setComponent($name, $component)
    {
        $this->components[$name] = $component;
        $this->setVariable($name, $component);
        return $this;
    }




    protected function getDefaultComponentManager()
    {
        return new ComponentManager();
    }



    protected function injectResources()
    {


        $this->componentManager->registerComponent($this);


        $javascriptAnchor = $this->dom->find($this->bodyEndSelector);


        $javascripts = $this->componentManager->getJavascripts();


        foreach ($javascripts as $javascript) {
            $javascriptKey = $javascript->getSource();

            if(!isset($this->injectedJavascripts[$javascriptKey])) {
                $this->injectedJavascripts[$javascriptKey] = true;
                $javascriptAnchor->before($javascript);
                $javascriptAnchor->before("\n");
            }
        }

        $cssList = $this->componentManager->getCSS();
        foreach ($cssList as $css) {
            $this->dom->head->append($css->render()."\n");
        }

    }


    public function compile()
    {
        parent::compile();

    }

    public function render()
    {

        $buffer = parent::render();
        return $buffer;

    }



    public function addLocalCSSFile($css)
    {

        $filepathRoot = $this->getApplication()->get('filepath-root');

        $css = str_replace($filepathRoot, '', $css);


        $engine = $this->getFromContainer('encrypt-engine');

        $cryptedCSS = $engine->encrypt($css);

        $cssLoaderURL = $this->getFromContainer('css-loader-url' );

        $url = $cssLoaderURL.'&css='.$cryptedCSS;
        $cssInstance = $this->addCSSFile($url);

        $cssInstance->setKey($css);

        return $cssInstance;
    }


    public function addLocalJavascriptFile($javascript)
    {
        $filepathRoot = Application::getInstance()->get('filepath-root');

        $javascript = str_replace($filepathRoot, '', $javascript);


        $engine = $this->getFromContainer('encrypt-engine');

        $cryptedJavascript = $engine->encrypt($javascript);

        $cssLoaderURL = $this->getFromContainer('javascript-loader-url' );

        $url = $cssLoaderURL.'&javascript='.$cryptedJavascript;
        $javascriptInstance = $this->addJavascriptFile($url);

        $javascriptInstance->setKey($javascript);
        return $javascriptInstance;
    }




}
