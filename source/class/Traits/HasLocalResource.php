<?php


 namespace Planck\Traits;


use Phi\HTML\CSSFile;
use Phi\HTML\JavascriptFile;
use Planck\View\Package;

trait HasLocalResource
{

    /**
     * @var Package[]
     */
    protected $frontPackages = [];




    public function addFrontPackage(Package $package)
    {
        $key = get_class($package);
        $this->frontPackages[$key] = $package;
        return $this;
    }


    /**
     * @return Package[]
     */
    public function getFrontPackages()
    {
        return $this->frontPackages;
    }


    /*
    //used for development concerns

    public function getLocalCSSFile($css)
    {

        $css = realpath($css);
        $filepathRoot = $this->getApplication()->get('filepath-root');
        $css = str_replace($filepathRoot, '', $css);


        $engine = $this->getFromContainer('encrypt-engine');

        $cryptedCSS = $engine->encrypt($css);

        $cssLoaderURL = $this->getFromContainer('css-loader-url' );

        $url = $cssLoaderURL.'&css='.$cryptedCSS;


        $data = array(
            'data-name' => $css
        );
        $data = null;
        $cssInstance = new CSSFile($url, $data);


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

        $loaderURL = $this->getFromContainer('javascript-loader-url' );

        $url = $loaderURL.'&javascript='.$cryptedJavascript;

        $data = array(
           'data-name' => $javascript
        );

        $data = null;

        $javascriptInstance = new JavascriptFile($url, $data);

        $javascriptInstance->setKey($javascript);
        return $javascriptInstance;
    }
    */


}