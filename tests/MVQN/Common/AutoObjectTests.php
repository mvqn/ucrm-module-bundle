<?php

use MVQN\Annotations\AnnotationReader;

use MVQN\Common\AutoObject;
use Tests\MVQN\Common\Examples\Country;

class AutoObjectTests extends PHPUnit\Framework\TestCase
{
    /** @var Country $country */
    protected $country;

    protected function setUp()
    {
        $this->country = new Country([ "id" => 249, "name" => "United States", "code" => "US"]);
    }


    public function testAutoGetters()
    {
        $id = $this->country->getId();
        $name = $this->country->getName();
        $code = $this->country->getCode();

        echo "AutoObjectTests->testAutoGetters()\n";
        echo "Country->getId()   : $id\n";
        echo "Country->getName() : $name\n";
        echo "Country->getCode() : $code\n";
        
        $this->assertEquals(249, $id);
        $this->assertEquals("United States", $name);
        $this->assertEquals("US", $code);
    }

    public function testAutoSetters()
    {
        $country = $this->country
            ->setTest("This is a test!");

        echo "AutoObjectTests->testAutoSetters()\n";
        echo "Country->setTest()   : {$country->getTest()}\n";

        $this->assertEquals("This is a test!", $country->getTest());
    }




}