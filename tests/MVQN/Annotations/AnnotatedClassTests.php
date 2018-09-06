<?php

use Tests\MVQN\Annotations\Examples\Country;

class AnnotatedClassTests extends PHPUnit\Framework\TestCase
{

    public function test__construct()
    {
        $country = new Country();

        print_r($country->annotationCache);

    }

}
