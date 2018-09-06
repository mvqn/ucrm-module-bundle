<?php

use MVQN\Annotations\AnnotationReader;
use Tests\MVQN\Annotations\Examples\Country;

class AnnotationReaderTests extends PHPUnit\Framework\TestCase
{

    public function test__construct()
    {
        // Class DocBlock
        $class = new AnnotationReader(Country::class);
        $this->assertNotNull($class);

        // Method DocBlock
        $method = $class->getMethodAnnotations("getName");
        $this->assertNotNull($method);

        // Property DocBlock
        $property = $class->getPropertyAnnotations("name");
        $this->assertNotNull($property);
    }


}
