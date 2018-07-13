<?php

namespace CRMConnector\Upgrade;


/**
 * Class CrmConnectorUpgrade
 * @package CRMConnector\Upgrade
 *
 * @author Josh Crawmer <joshcrawmer@gmail.com>
 */
class CrmConnectorUpgrade
{


    private $migrations_dir;


    public function __construct()
    {
        $this->setMigrationsDir();
    }



    public function run()
    {
    }


    private function setMigrationsDir()
    {
        $this->migrations_dir = sprintf('%s/Migrations/', __DIR__);
    }


}