<?php

use MVQN\Annotations\{ClassAnnotationReader, AnnotationReaderException};
use Tests\MVQN\Annotations\Examples\Country;

class AnnotationReaderTests extends PHPUnit\Framework\TestCase
{

    public function test__construct()
    {
        // Class DocBlock
        $class = new ClassAnnotationReader(Country::class);
        $this->assertNotNull($class);

        // Property DocBlock
        $property = new ClassAnnotationReader(Country::class, "name", "property");
        $this->assertNotNull($property);

        // Method DocBlock
        $method = new ClassAnnotationReader(Country::class, "getName", "method");
        $this->assertNotNull($method);
    }

    public function testGetParameters()
    {
        // Class DocBlock
        $class = new ClassAnnotationReader(Country::class);
        $this->assertNotNull($class);

        $parameters = $class->getParameters();
        echo json_encode($parameters, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("/countries/:id", $parameters["endpoints"]["getById"]);

        // Property DocBlock
        $property = new ClassAnnotationReader(Country::class, "name", "property");
        $this->assertNotNull($property);

        $parameters = $property->getParameters();
        echo json_encode($parameters, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("string|null \$name The country's name.", $parameters["var"]);

        // Method DocBlock
        $method = new ClassAnnotationReader(Country::class, "getName", "method");
        $this->assertNotNull($method);

        $parameters = $method->getParameters();
        echo json_encode($parameters, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("string|null Returns the country's name.", $parameters["return"]);

        echo "\n";
    }

    public function testGetParameter()
    {
        // Class DocBlock
        $class = new ClassAnnotationReader(Country::class);
        $this->assertNotNull($class);

        $parameter = $class->getParameter("author");
        echo json_encode($parameter, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("Ryan Spaeth <rspaeth@mvqn.net>", $parameter);

        // Property DocBlock
        $property = new ClassAnnotationReader(Country::class, "name", "property");
        $this->assertNotNull($property);

        $parameter = $property->getParameter("var");
        echo json_encode($parameter, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("string|null \$name The country's name.", $parameter);

        // Method DocBlock
        $method = new ClassAnnotationReader(Country::class, "getName", "method");
        $this->assertNotNull($method);

        $parameter = $method->getParameter("return");
        echo json_encode($parameter, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("string|null Returns the country's name.", $parameter);

        echo "\n";
    }

    public function testHasParameter()
    {
        // Class DocBlock
        $class = new ClassAnnotationReader(Country::class);
        $this->assertNotNull($class);

        $parameter = $class->hasParameter("author");
        $this->assertEquals(true, $parameter);

        // Property DocBlock
        $property = new ClassAnnotationReader(Country::class, "name", "property");
        $this->assertNotNull($property);

        $parameter = $property->hasParameter("var");
        $this->assertEquals(true, $parameter);

        // Method DocBlock
        $method = new ClassAnnotationReader(Country::class, "getName", "method");
        $this->assertNotNull($method);

        $parameter = $method->hasParameter("return");
        $this->assertEquals(true, $parameter);

        // Method DocBlock
        $method = new ClassAnnotationReader(Country::class, "getName", "method");
        $this->assertNotNull($method);

        $parameter = $method->hasParameter("param");
        $this->assertEquals(false, $parameter);
    }

    public function testGetPropertyInfo()
    {
        // Property DocBlock
        $property = new ClassAnnotationReader(Country::class, "name", "property");
        $this->assertNotNull($property);

        $parameters = $property->getPropertyInfo();
        echo json_encode($parameters, JSON_UNESCAPED_SLASHES)."\n";
        $this->assertEquals("The country's name.", $parameters["description"]);

        echo "\n";
    }

    public function testReflectionException()
    {
        $this->expectException(\ReflectionException::class);

        try
        {
            // Property DocBlock
            $property = new ClassAnnotationReader(Country::class, "unknown", "property");
            $this->assertNotNull($property);
        }
        catch(\ReflectionException $re)
        {
            echo $re->getMessage();
            throw $re;
        }
    }

    public function testAnnotationReaderException()
    {
        $this->expectException(AnnotationReaderException::class);

        try
        {
            // Property DocBlock
            $method = new ClassAnnotationReader(Country::class, "getName", "method");
            $this->assertNotNull($method);

            $parameters = $method->getPropertyInfo();
            echo json_encode($parameters, JSON_UNESCAPED_SLASHES)."\n";
            $this->assertEquals("The country's name.", $parameters["description"]);
        }
        catch(AnnotationReaderException $are)
        {
            echo $are->getMessage();
            throw $are;
        }
    }

}
