<?php
declare(strict_types=1);

namespace Tests\MVQN\Common\Examples;


use MVQN\Annotations\AnnotationReader;
use MVQN\Common\AutoObject;

class Endpoint extends AutoObject
{
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }


    /*
    public function validate(): bool
    {
        $annotations = new AnnotationReader(Country::class);
        $test = $annotations->getPropertyAnnotations("code");

        $conditional = str_replace("`", "", $test["PostRequired"]);

        $result = eval("return $conditional;");

        echo "";

        return true;
    }
    */



    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        // Add each provided key as a property with the given value to this object...
        foreach($values as $key => $value)
            $this->$key = $value;
    }

}