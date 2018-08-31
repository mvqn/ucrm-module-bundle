<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use MVQN\Annotations\AnnotationReaderException;
use MVQN\Collections\CollectionException;
use MVQN\Common\ArraysException;
use MVQN\Common\PatternsException;

use MVQN\REST\Endpoints\EndpointException;
use MVQN\REST\RestClientException;

use UCRM\REST\Endpoints\{Country, State};

/**
 * Trait InvoiceStateHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait InvoiceStateHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return State|null
     * @throws AnnotationReaderException
     * @throws ArraysException
     * @throws EndpointException
     * @throws PatternsException
     * @throws RestClientException
     */
    public function getInvoiceState(): ?State
    {
        if(property_exists($this, "invoiceStateId") && $this->{"invoiceStateId"} !== null)
            $state = State::getById($this->{"invoiceStateId"});

        /** @var State|null $state */
        return $state;
    }

    /**
     * @param State $value
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    public function setInvoiceState(State $value): self
    {
        $this->{"invoiceStateId"} = $value->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $name
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws AnnotationReaderException
     * @throws ArraysException
     * @throws CollectionException
     * @throws EndpointException
     * @throws PatternsException
     * @throws RestClientException
     */
    public function setInvoiceStateByName(string $name): self
    {
        if(!property_exists($this, "invoiceCountryId") || $this->{"invoiceCountryId"} === null)
            throw new EndpointException("Country ID must be set before the use of Client->setStateByName()!");

        /** @var Country $country */
        $country = Country::getById($this->{"invoiceCountryId"});

        /** @var State $state */
        $state = $country->getStates()->where("name", $name)->first();
        $this->{"invoiceStateId"} = $state->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $code
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws AnnotationReaderException
     * @throws ArraysException
     * @throws CollectionException
     * @throws EndpointException
     * @throws PatternsException
     * @throws RestClientException
     */
    public function setInvoiceStateByCode(string $code): self
    {
        if(!property_exists($this, "invoiceCountryId") || $this->{"invoiceCountryId"} === null)
            throw new EndpointException("Country ID must be set before the use of Client->setStateByName()!");

        /** @var Country $country */
        $country = Country::getById($this->{"invoiceCountryId"});

        /** @var State $state */
        $state = $country->getStates()->where("code", $code)->first();
        $this->{"invoiceStateId"} = $state->getId();

        /** @var self $this */
        return $this;
    }
}