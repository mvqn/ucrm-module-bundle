<?php
declare(strict_types=1);

namespace Tests\MVQN\Common\Examples;

use MVQN\Common\AutoObject;
use MVQN\REST\Annotations\EndpointAnnotation;
use MVQN\REST\Annotations\EndpointAnnotation as Get;
use MVQN\REST\Annotations\EndpointAnnotation as Post;

use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PostRequiredWhenAnnotation as PostRequiredWhen;
use Tests\MVQN\Common\Examples\Helpers\CountryHelper;

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
 * @method string|null getName()
 * @method string|null getCode()
 */
final class Country extends Endpoint
{
    use CountryHelper;

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








}
