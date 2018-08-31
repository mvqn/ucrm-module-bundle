<?php
declare(strict_types=1);

namespace Tests\MVQN\Common\Examples;

use MVQN\Common\AutoObject;
use MVQN\REST\Annotations\EndpointAnnotation;
use MVQN\REST\Annotations\EndpointAnnotation as Get;
use MVQN\REST\Annotations\EndpointAnnotation as Post;

use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PostRequiredWhenAnnotation as PostRequiredWhen;

/**
 * Class Country
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Get { "get": "/countries", "getById": "/countries/:id" }
 * @Post [ "post" => "/countries" ]
 * @EndpointAnnotation
 * @MVQN\REST\Annotations\EndpointAnnotation { "patch": "/countries/:id" }
 *
 * @post-required-when `$name === "United States"`
 *
 * @property int $id Country ID
 * @property-read string $name Country Name
 * @property-write string $code Country Abbreviation
 *
 */
final class Country extends Endpoint
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired
     */
    protected $name;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired `$this->name === "United States"`
     */
    protected $code;





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
