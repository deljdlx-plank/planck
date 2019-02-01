<?php

namespace {{{namespace}}}\Container;

use Planck\Container;

class Path extends Container
{
    public function initialize()
    {





        $this->set('test-host', '{{{host-test}}}');


        $this->set('url-root', function() {
            return '{{{url-root}}}';
        });

        $this->set('filepath-root', function () {
            return realpath(__DIR__.'/../../..');
        });


        $this->set('public-filepath-root', function () {
            return $this->get('filepath-root').'/public';
        });


        $this->set('data-filepath-root', function () {
            return $this->get('filepath-root').'/data';
        });

        //=======================================================
        $this->set('lang-filepath-root', function () {
            return $this->get('data-filepath-root').'/lang';
        });


        $this->set('default-lang', function () {
            return '__default';
        });

        //=======================================================

        $this->set('user-data-filepath-root', function () {
            return $this->get('filepathRoot').'/public/data';
        });

        $this->set('user-data-url-root', function () {
            return $this->get('url-root').'/data';
        });
        //=======================================================




    }

}