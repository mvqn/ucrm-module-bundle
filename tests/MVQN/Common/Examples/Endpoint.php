<?php
declare(strict_types=1);

namespace Tests\MVQN\Common\Examples;


use MVQN\Annotations\AnnotationReader;

class Endpoint
{




    public function validate(): bool
    {
        $annotations = new AnnotationReader(Country::class);
        $test = $annotations->getPropertyAnnotations("code");

        $conditional = str_replace("`", "", $test["PostRequired"]);

        $result = eval("return $conditional;");

        echo "";

        return true;
    }


}