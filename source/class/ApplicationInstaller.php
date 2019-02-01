<?php

namespace Planck;

use Phi\Component\Traits\MustacheTemplate;

class ApplicationInstaller
{

    use MustacheTemplate;

    protected $applicationPath;

    protected $database;

    public function __construct($path)
    {

        $this->applicationPath = realpath($path);

        if(!is_dir($this->applicationPath)) {
            throw new Exception('Application path '.$this->applicationPath.' does not exist');
        }
    }


    public function install()
    {
        $this->initializeContainers();

        $this->initializeLangPath();



        $this->initializeDatabase($this->getDatabasePath());
        $this->loadDatabase($this->getDatabasePath());

        $this->executeSQLInPath($this->applicationPath.'/install/model/create-table');
        $this->executeSQLInPath($this->applicationPath.'/install/model/provision');
    }

    public function initializeContainers()
    {

        $template = file_get_contents(__DIR__.'/../../data/AppliationInstaller/Container/Main.template.php');

        $configuration = $this->compileMustache($template, array(
            'namespace' => 'PlanckeyBlog',
            'encryptKey' => uniqid(),
            'encryptKeyIv' => uniqid(),
            'smtp' => array(
                'host' => 'smtp.gmail.com',
                'port' => 465,
                'encryption' => 'ssl',
                'user' => 'planckify@gmail.com',
                'password' => '1664isgood'

            ),
        ));

        echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
        echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
        print_r(highlight_string($configuration, true));
        echo '</pre>';

        file_put_contents($this->applicationPath.'/source/class/Container/Main.php', $configuration);



        $template = file_get_contents(__DIR__.'/../../data/AppliationInstaller/Container/Path.template.php');

        $configuration = $this->compileMustache($template, array(
            'namespace' => 'PlanckeyBlog',
            'host-test' => 'http://planckify.jlb.ninja',
            'url-root' => 'http://192.168.0.10/project/planck/Application/Planck/public',
            'smtp' => array(
                'host' => 'smtp.gmail.com',
                'port' => 465,
                'encryption' => 'ssl',
                'user' => 'planckify@gmail.com',
                'password' => '1664isgood'

            ),
        ));
        file_put_contents($this->applicationPath.'/source/class/Container/Path.php', $configuration);

        echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
        echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
        print_r(highlight_string($configuration, true));
        echo '</pre>';



        die('EXIT '.__FILE__.'@'.__LINE__);

    }


    public function getDatabasePath()
    {
        return $this->applicationPath.'/data/main.sqlite';
    }



    public function initializeDatabase($path = null)
    {
        $dsn = 'sqlite:' . $path;
        $driver = new \Phi\Database\Driver\PDO\Source($dsn);
        $database = new \Phi\Database\Source($driver);
        return $database;
    }



    public function executeSQLInPath($path)
    {

        if(!is_dir($path)) {
            return false;
        }

        $scripts = glob($path.'/*.sql');

        foreach ($scripts as $sqlFile) {
            $sql = file_get_contents($sqlFile);
            $this->executeSQL($sql);
        }
    }



    public function loadDatabase($path)
    {
        $dsn = 'sqlite:' . $path;
        $driver = new \Phi\Database\Driver\PDO\Source($dsn);
        $database = new \Phi\Database\Source($driver);
        $this->database = $database;

        return $this->database;
    }






    public function executeSQL($sql)
    {
        $this->database->query($sql);
    }

    public function initializeLangPath()
    {
        $langPath = $this->applicationPath.'/data/lang/__default';
        if(!is_dir($langPath)) {
            mkdir($langPath, 0555, true);
        }
        if(!is_file($langPath.'/main.php')) {
            $buffer = "<?php


return array(

    'helloWorld' => 'Hello world',
);
            ";
            file_put_contents($langPath.'/main.php', $buffer);
        }
    }


}


