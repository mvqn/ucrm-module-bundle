<?php
declare(strict_types=1);

namespace MVQN\Common;


use MVQN\Annotations\AnnotationReader;
use MVQN\Annotations\ClassAnnotationReader;

/**
 * Class AutoObject
 *
 * @package MVQN\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class AutoObject
{

    /**
     * @var array An array that stores the annotation for each @property, including -read and -write by property.
     */
    private $propertyCache = null;




    private function buildPropertyCache(): void
    {
        $annotations = new AnnotationReader(get_class($this));

        //$properties = $annotations->getParameter("property");
        //$properties = $annotations->getParametersLike("/property-?\w*/");
        $properties = $annotations->getClassAnnotationsLike("/property-?\w*/");

        echo "";
    }


    public function __get(string $property)
    {
        if($this->propertyCache === null)
        {
            // Get the class @property values for read/write exclusions...
            $this->buildPropertyCache();
        }


        if (method_exists($this, $property))
        {
            return $this->$property();
        }
        elseif (property_exists($this, $property))
        {
            // Getter/Setter not defined so return property if it exists
            return $this->$property;
        }
        else
        {
            return null;
        }
    }





}