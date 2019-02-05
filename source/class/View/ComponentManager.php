<?php

namespace Planck\View;

class ComponentManager
{

    /**
     * @var Component[]
     */
    protected $components = array();

    public function __construct()
    {


    }

    public function registerComponent(\Phi\HTML\Component $component)
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

        $tempJavascripts = [];

        foreach ($components as $component) {
            foreach ($component->getJavascriptTags() as $priority => $javascriptsPerPriority) {

                if(!array_key_exists($priority, $tempJavascripts)) {
                    $tempJavascripts[$priority] = [];
                }

                $tempJavascripts[$priority] = array_merge(
                    $tempJavascripts[$priority],
                    $javascriptsPerPriority
                );
            }
        }

        krsort($tempJavascripts);

        $javascripts = $this->mergeResourceArray($tempJavascripts);

        return $javascripts;
    }


    private function mergeResourceArray($resourceArray)
    {

        $injectedResources = [];
        $resources = [];

        foreach ($resourceArray as $resourcesPerPriority) {
            foreach ($resourcesPerPriority as $resource) {
                $key = $resource->getSource();
                if(!isset($injectedResources[$key])) {
                    $injectedResources[$key] = true;
                    $resources[] = $resource;
                }
            }
        }
        return $resources;
    }






    public function getCSS()
    {

        $components = $this->getComponents();

        $tempCSS = [];

        foreach ($components as $component) {
            foreach ($component->getCSSTags() as $priority => $cssPerPriority) {

                if(!array_key_exists($priority, $tempCSS)) {
                    $tempCSS[$priority] = [];
                }

                $tempCSS[$priority] = array_merge(
                    $tempCSS[$priority],
                    $cssPerPriority
                );
            }
        }

        krsort($tempCSS);

        $css = $this->mergeResourceArray($tempCSS);


        return $css;







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