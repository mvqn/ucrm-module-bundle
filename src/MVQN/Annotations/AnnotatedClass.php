<?php
declare(strict_types=1);

namespace MVQN\Annotations;

use MVQN\Collections\Collectible;

/**
 * Class Annotation
 *
 * @package MVQN\Annotations
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class AnnotatedClass extends Collectible
{
    /**
     * @var array An array of cached annotations for this class to be used to speed up look-ups in future calls.
     */
    public $annotationCache = [];

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

    /**
     * AnnotatedClass constructor.
     *
     * @throws AnnotationReaderException
     */
    public function __construct()
    {
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





}