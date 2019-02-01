<?php

namespace {{{namespace}}}\Container;

use Planck\Container;
use Planck\Tool\Encrypt;
use Planck\View\ComponentManager;

class Main extends Container
{
    public function initialize()
    {



        $this->set('database', function() {
            $dsn = 'sqlite:' . __DIR__.'/../../../data/database/main.sqlite';
            $driver = new \Phi\Database\Driver\PDO\Source($dsn);
            $database = new \Phi\Database\Source($driver);
            return $database;
        });

        $this->set('model', function() {
            $database = $this->get('database');
            $model = new \Planck\Model\Model();
            $model->addSource($database);
            return $model;
        });



        $this->set('encrypt-engine', function() {
            $engine = new Encrypt('{{{encryptKey}}}', '{{{encryptKeyIv}}}');
            return $engine;
        });

        $this->set('view-component-manager', function() {
            $manager = new ComponentManager();
            return $manager;
        });

        $this->set('css-loader-url', '?/tool/api-get-css');
        $this->set('javascript-loader-url', '?/tool/api-get-javascript');


        $this->set('mailer', function () {

            // Create the Transport
            //https://www.google.com/settings/u/1/security/lesssecureapps
            //https://accounts.google.com/b/0/DisplayUnlockCaptcha
            $transport = (new \Swift_SmtpTransport('{{{smtp.host}}}', {{{smtp.port}}}, '{{{smtp.encryption}}}'))
                ->setUsername('{{{smtp.user}}}')
                ->setPassword('{{{smtp.password}}}')
            ;

            $mailer = new \Swift_Mailer($transport);
            return $mailer;

        });


    }

}