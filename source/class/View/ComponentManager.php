<?php

namespace Planck\View;

class ComponentManager
{

    /**
     * @var Component[]
     */
    protected $components = array();

    public function registerComponent(Component $component)
    {
        $this->components[] = $component;
        return $this;
    }


    public function getComponents()
    {
        return $this->components;
    }

    public function getJavascripts()
    {
        $components = $this->getComponents();

        $javascripts = [];
        $injectedJavascripts = [];

        foreach ($components as $component) {
            foreach ($component->getJavascriptTags() as $javascript) {

                $javascriptKey = $javascript->getSource();
                if(!isset($injectedJavascripts[$javascriptKey])) {
                    $injectedJavascripts[$javascriptKey] = true;
                    $javascripts[] = $javascript;
                }
            }
        }


        return $javascripts;
    }

    public function getCSS()
    {
        $components = $this->getComponents();

        $css = [];
        $injectedCSS = [];

        foreach ($components as $component) {

            foreach ($component->getCSSTags() as $cssTag) {

                $key = $cssTag->getSource();
                if(!isset($injectedCSS[$key])) {
                    $injectedCSS[$key] = true;
                    $css[] = $cssTag;
                }
            }
        }
        return $css;
    }


}