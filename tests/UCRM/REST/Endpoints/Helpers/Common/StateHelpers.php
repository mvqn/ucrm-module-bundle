<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use MVQN\Annotations\AnnotationReaderException;
use MVQN\Collections\CollectionException;
use MVQN\Common\ArraysException;
use MVQN\Common\PatternsException;

use MVQN\REST\RestClientException;
use MVQN\REST\Endpoints\EndpointException;

use UCRM\REST\Endpoints\{Country, State};

/**
 * Trait StateHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait StateHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return State|null
     * @throws AnnotationReaderException
     * @throws ArrayHelperException
     * @throws EndpointException
     * @throws PatternMatchException
     * @throws RestClientException
     * @throws \ReflectionException
     */
    public function getState(): ?State
    {
        if(property_exists($this, "stateId") && $this->{"stateId"} !== null)
            $state = State::getById($this->{"stateId"});

        /** @var State|null $state */
        return $state;
    }

    /**
     * @param State $value
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    public function setState(State $value): self
    {
        $this->{"stateId"} = $value->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $name
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws AnnotationReaderException
     * @throws ArrayHelperException
     * @throws CollectionException
     * @throws EndpointException
     * @throws PatternMatchException
     * @throws RestClientException
     * @throws \ReflectionException
     */
    public function setStateByName(string $name): self
    {
        if(!property_exists($this, "countryId") || $this->{"countryId"} === null)
            throw new EndpointException("Country ID must be set before the use of Client->setStateByName()!");

        /** @var Country $country */
        $country = Country::getById($this->{"countryId"});

        /** @var State $state */
        $state = $country->getStates()->where("name", $name)->first();
        $this->{"stateId"} = $state->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $code
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws AnnotationReaderException
     * @throws ArrayHelperException
     * @throws CollectionException
     * @throws EndpointException
     * @throws PatternMatchException
     * @throws RestClientException
     * @throws \ReflectionException
     */
    public function setStateByCode(string $code): self
    {
        if(!property_exists($this, "countryId") || $this->{"countryId"} === null)
            throw new EndpointException("Country ID must be set before the use of Client->setStateByName()!");

        /** @var Country $country */
        $country = Country::getById($this->{"countryId"});

        /** @var State $state */
        $state = $country->getStates()->where("code", $code)->first();
        $this->{"stateId"} = $state->getId();

        /** @var self $this */
        return $this;
    }
}