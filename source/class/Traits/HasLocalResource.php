<?php


 namespace Planck\Traits;


use Phi\HTML\CSSFile;
use Phi\HTML\JavascriptFile;

trait HasLocalResource
{


    public function getLocalCSSFile($css)
    {

        $css = realpath($css);
        $filepathRoot = $this->getApplication()->get('filepath-root');
        $css = str_replace($filepathRoot, '', $css);


        $engine = $this->getFromContainer('encrypt-engine');

        $cryptedCSS = $engine->encrypt($css);

        $cssLoaderURL = $this->getFromContainer('css-loader-url' );

        $url = $cssLoaderURL.'&css='.$cryptedCSS;
        $cssInstance = new CSSFile($url);

        $cssInstance->setKey($css);

        return $cssInstance;
    }


    public function getLocalJavascriptFile($javascript)
    {

        $javascript = realpath($javascript);

        $filepathRoot = $this->getApplication()->get('filepath-root');

        $javascript = str_replace($filepathRoot, '', $javascript);

        $engine = $this->getFromContainer('encrypt-engine');

        $cryptedJavascript = $engine->encrypt($javascript);

        $cssLoaderURL = $this->getFromContainer('javascript-loader-url' );

        $url = $cssLoaderURL.'&javascript='.$cryptedJavascript;
        $javascriptInstance = new JavascriptFile($url);

        $javascriptInstance->setKey($javascript);
        return $javascriptInstance;
    }


}