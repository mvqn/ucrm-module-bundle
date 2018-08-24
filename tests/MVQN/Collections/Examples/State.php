<?php
declare(strict_types=1);

namespace Tests\MVQN\Collections\Examples;

use MVQN\Collections\Collectible;

/**
 * Class State
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @endpoints { "get": "", "getById": "/countries/states/:id" }
 */
final class State extends Collectible
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @var int
     */
    protected $countryId;

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @var string
     */
    protected $code;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }


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

