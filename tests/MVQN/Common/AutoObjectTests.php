<?php

use MVQN\Annotations\AnnotationReader;

use MVQN\Common\AutoObject;
use Tests\MVQN\Common\Examples\Country;

class AutoObjectTests extends PHPUnit\Framework\TestCase
{


    public function test__get()
    {
        $country = new Country([ "name" => "United States", "code" => "US"]);

        //$name = $country->name;

        //$annotations = new AnnotationReader(Country::class);
        //$test = $annotations->getPropertyAnnotations("code");

        //$valid = $country->validate();

        //echo $name."\n";

        echo $country->getName()."\n";
        echo $country->getBoth()."\n";

        //$country->setName("Test");
        //echo $country->getName()."\n";

        echo $country->getId()."\n";

        echo "";
    }
}