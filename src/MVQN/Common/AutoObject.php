<?php
declare(strict_types=1);

namespace MVQN\Common;


use MVQN\Annotations\AnnotationReader;
use MVQN\Collections\Collectible;

/**
 * Class AutoObject
 *
 * @package MVQN\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class AutoObject extends Collectible
{
    /**
     * @var array An array of cached annotations for this class to be used to speed up look-ups in future calls.
     */
    private $annotationCache = null;

    /**
     * @const array a list of methods for which to ignore when parsing annotations.
     */
    private const IGNORE_METHODS = [
        "__construct",
        "__toString",
        "jsonSerialize"
    ];

    /**
     * @const array a list of properties for which to ignore when parsing annotations.
     */
    private const IGNORE_PROPERTIES = [
        "annotationCache"
    ];


    private const ANNOTATION_READONLY = "readonly";
    private const ANNOTATION_WRITEONLY = "writeonly";




    private function buildAnnotationCache(): void
    {
        $this->annotationCache = [];

        // Instantiate an Annotation Reader!
        $annotationReader = new AnnotationReader(get_class($this));

        // Read and store all class annotations...
        $this->annotationCache["class"] = $annotationReader->getClassAnnotations();

        // Read and store all class method annotations...
        $this->annotationCache["methods"] = [];
        $methods = $annotationReader->getReflectedClass()->getMethods();
        foreach($methods as $method)
        {
            $name = $method->getName();

            if(!in_array($name, self::IGNORE_METHODS))
                $this->annotationCache["methods"][$name] = $annotationReader->getMethodAnnotations($name);
        }

        // Read and store all class property annotations...
        $this->annotationCache["properties"] = [];
        $properties = $annotationReader->getReflectedClass()->getProperties();
        foreach($properties as $property)
        {
            $name = $property->getName();

            if(!in_array($name, self::IGNORE_PROPERTIES))
                $this->annotationCache["properties"][$name] = $annotationReader->getPropertyAnnotations($name);
        }
    }


    public function __call(string $name, array $args)
    {
        // Build the cache for this class, if it has not already been done!
        if($this->annotationCache === null)
            $this->buildAnnotationCache();

        // Check to see if a real method already exists for the requested __call()...
        if(method_exists($this, $name))
            return $name($args);

        $class = get_class($this);

        // Handle the cases, where the method called begins with 'get'...
        if(Strings::startsWith($name, "get"))
        {
            $property = lcfirst(str_replace("get", "", $name));

            if(!array_key_exists("class", $this->annotationCache) ||
                !array_key_exists("method", $this->annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            $regex = "/^(?:[\w\|\[\]]*)?\s+(get\w*)\s*\(.*\).*$/";
            $found = false;

            foreach ($this->annotationCache["class"]["method"] as $annotation)
            {
                if(preg_match($regex, $annotation, $matches))
                {
                    if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($this, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            return $this->{$property};
        }

        if(Strings::startsWith($name, "set"))
        {
            $property = lcfirst(str_replace("set", "", $name));

            if(!array_key_exists("class", $this->annotationCache) ||
                !array_key_exists("method", $this->annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            $regex = "/^(?:[\w\|\[\]]*)?\s+(set\w*)\s*\(.*\).*$/";
            $found = false;

            foreach ($this->annotationCache["class"]["method"] as $annotation)
            {
                if(preg_match($regex, $annotation, $matches))
                {
                    if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($this, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            $this->{$property} = $args[0];
            return $this;
        }

        throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
            $class."'!");
    }

    public static function __callStatic(string $name, array $args)
    {

    }





}