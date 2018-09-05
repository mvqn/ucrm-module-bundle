<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\Endpoints\Endpoint;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoints;
use UCRM\REST\Endpoints\Helpers\CountryHelper;

/**
 * Class Country
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoints { "get": "/countries", "getById": "/countries/:id" }
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
     */
    protected $name;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $code;

}
