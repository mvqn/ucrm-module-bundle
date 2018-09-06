<?php
declare(strict_types=1);

namespace MVQN\REST\Endpoints;

use MVQN\Annotations\AnnotationReader;
use MVQN\Collections\Collection;
use MVQN\Common\{Arrays, Patterns};

use MVQN\REST\{RestObject, RestClient};

/**
 * Class Endpoint
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class Endpoint extends RestObject
{
    // =================================================================================================================
    // PROPERTIES (GLOBAL)
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int $id
     * @unique
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    // =================================================================================================================
    // CREATE (GLOBAL)
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Attempts to INSERT this Endpoint object, using the class's annotated information via a HTTP POST Request.
     *
     * @return self Returns an Endpoint of the same child object that called this method.
     *
     * @throws \Exception
     */
    public function insert(): self
    {
        /** @var self $data */
        $data = $this;

        if(!$this->validate("post", $missing))
        {
            throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Annotations for the '".get_class($this)."' class require valid values be set ".
                "on all of the following properties before attempting an insert():\n> ".implode("\n> ", $missing)."\n");
        }

        /** @var self $endpoint */
        $endpoint = self::post($data);
        return $endpoint;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends a HTTP POST Request on behalf of the Endpoint, given optional parameters.
     *
     * @param Endpoint $data The data to be passed in the body of the request.
     * @param array $params An optional set of parameters to be interpolated into the URL, when placeholders are used.
     * @return Endpoint Returns the newly POSTed Endpoint.
     *
     * @throws \Exception
     */
    public static function post(Endpoint $data, array $params = []): Endpoint
    {
        $class = get_called_class();

        // Instantiate an AnnotationReader for this class AND check for the important parameters.
        $annotations = new AnnotationReader($class);
        $endpoints = $annotations->getClassAnnotation("endpoints");

        $endpoint = array_key_exists("post", $endpoints) ? $endpoints["post"] : "";

        if($endpoint === "")
            throw new \Exception("[MVQN\REST\Endpoints\Endpoint] An annotation like '@endpoints { \"post\": \"/examples\" }' on the '$class' ".
                "class must be declared in order to resolve this endpoint'");

        // Interpolate the URL patterns against any provided parameters.
        $endpoint = Patterns::interpolateUrl($endpoint, $params);

        // Get an array of all Model properties, with any that do not belong in the POST method removed!
        $data = ($data !== null) ? $data->toArray("post") : [];

        // Attempt to POST the specified Endpoint.
        $response = RestClient::post($endpoint, $data);

        // IF the response is empty, something went VERY wrong!
        if($response === [])
        {
            throw new \Exception("WTF???");
            //return [];
        }

        // HANDLE ANY ERROR CODES HERE...
        if(array_key_exists("code", $response))
        {
            switch($response["code"])
            {
                case 401: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The REST Client was not authorized to make this request!");
                case 403: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The provided App Key does not have sufficient privileges!");
                case 404: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Endpoint '$endpoint' was not found for class '$class'!");
                case 422: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Data for endpoint '$endpoint' was improperly formatted!\n".
                    $response["message"]."\n".
                    json_encode($response["errors"], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );

                // TODO: Add other response codes, as they are encountered!

                default:  break; // Likely the key "code" from an actual Endpoint!
            }
        }

        // Finally, return the instantiated Endpoint!
        return new $class($response);
    }

    // =================================================================================================================
    // READ (GLOBAL)
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for all objects at this Endpoint.
     *
     * @param string $override An optional URL to override the class's annotated endpoint information.
     * @param array $params An optional set of parameters to be interpolated into the URL, when placeholders are used.
     * @param array $query An optional set of query parameters to append to the end of the Endpoint URL.
     * @return Collection Returns a Collection of Endpoint objects; empty if none are found.
     *
     * @throws \Exception
     */
    public static function get(string $override = "", array $params = [], array $query = []): Collection
    {
        // Get a reference to the type of Endpoint calling this function.
        $class = get_called_class();

        // Set any flags here for special cases!
        $excludeId = false;

        // IF no override path has been provided...
        if($override === "")
        {
            // THEN instantiate an AnnotationReader for this class AND check for the important parameters.
            $annotations = new AnnotationReader($class);
            $endpoints = $annotations->getClassAnnotation("endpoints");
            $excludeId = $annotations->hasClassAnnotation("excludeId");
            //$singular = $annotations->hasParameter("singular");

            // Make certain we have found a valid set of GET annotations, or throw an error!
            if (!array_key_exists("get", $endpoints) || $endpoints["get"] === "")
                throw new \Exception("[MVQN\REST\Endpoints\Endpoint] An '@Endpoint { \"get\": \"/examples\" }' annotation on the class must " .
                    "be declared in order to resolve this endpoint'");

            // Interpolate the URL patterns against any provided parameters.
            $endpoint = Patterns::interpolateUrl($endpoints["get"], $params);
        }
        else
        {
            // Interpolate the overridden URL pattern against any provided parameters.
            $endpoint = Patterns::interpolateUrl($override, $params);
            //$endpoint = $override;
        }

        // Append any provided suffixes to the URL (i.e. query string).
        if($query !== [])
        {
            $pairs = [];

            foreach($query as $key => $value)
                $pairs[] = "$key=$value";

            $endpoint .= "?".implode("&", $pairs);
        }

        // Attempt to GET the specified Endpoint.
        $response = RestClient::get($endpoint);

        // IF the response is empty...
        if($response === [])
            // THEN return an empty collection, as we got nothing back!
            return new Collection($class, []);

        // HANDLE ANY ERROR CODES HERE...
        if(array_key_exists("code", $response))
        {
            switch($response["code"])
            {
                case 401: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The REST Client was not authorized to make this request!");
                case 403: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The provided App Key does not have sufficient privileges!");
                case 404: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Endpoint '$endpoint' was not found for class '$class'!");

                // TODO: Add other response codes, as they are encountered!

                default:  break; // Likely the key "code" from an actual Endpoint!
            }
        }

        // Handle shifting any single object responses into a single indexed array for further processing.
        $response = Arrays::is_assoc($response) ? [ $response ] : $response;

        // Create a collection to store the object versions of the response.
        $endpoints = new Collection($class);

        // Loop through each resulting object...
        foreach($response as $object)
        {
            $classObject = new $class($object);

            // Remove the ID property, if the '@excludeId' annotation was set on this class.
            if($excludeId)
                unset($classObject->id);

            // Add the newly instantiated Endpoint to the collection.
            $endpoints->push($classObject);
        }

        // Finally, return the collection of Endpoints!
        return $endpoints;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for a single object, given the ID.
     *
     * @param int $id The ID of the Endpoint for which to retrieve.
     * @return Endpoint|null Returns the Endpoint object, or NULL if the object can not be found with this ID.
     *
     * @throws \Exception
     */
    public static function getById(int $id): ?Endpoint
    {
        // Get a reference to the type of Endpoint calling this function.
        $class = get_called_class();

        // Instantiate an AnnotationReader for this class AND check for the important parameters.
        $annotations = new AnnotationReader($class);
        $endpoints = $annotations->getClassAnnotation("endpoints");

        // Make certain we have found a valid set of GET annotations, or throw an error!
        if(!array_key_exists("getById", $endpoints))
            throw new \Exception("[MVQN\REST\Endpoints\Endpoint] An '@EndpointAnnotation { \"getById\": \"/examples/:id\" }' annotation on ".
                "the '$class' class must be declared in order to resolve this endpoint'");

        // Interpolate the URL patterns against any provided parameters.
        $endpoint = Patterns::interpolateUrl($endpoints["getById"], [ "id" => $id ]);

        // Attempt to GET the specified Endpoint.
        $response = RestClient::get($endpoint);

        // IF the response is empty...
        if($response === [])
            // THEN return NULL, as we got nothing back!
            return null;

        // HANDLE ANY ERROR CODES HERE...
        if(array_key_exists("code", $response))
        {
            switch($response["code"])
            {
                case 401: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The REST Client was not authorized to make this request!");
                case 403: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The provided App Key does not have sufficient privileges!");
                case 404: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Endpoint '$endpoint' was not found for class '$class'!");

                // TODO: Add other response codes, as they are encountered!

                default:  break; // Likely the key "code" from an actual Endpoint!
            }
        }

        // Finally, return the instantiated Endpoint!
        return new $class($response);
    }

    // =================================================================================================================
    // UPDATE (GLOBAL)
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Attempts to UPDATE this Endpoint object, using the class's annotated information via a HTTP PATCH Request.
     *
     * @return self Returns an Endpoint of the same child object that called this method.
     *
     * @throws \Exception
     */
    public function update(): self
    {
        /** @var self $data */
        $data = $this;

        if(!$this->validate("patch", $missing))
        {
            throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Annotations for the '".get_class($this)."' class require valid values be set ".
                "on all of the following properties before attempting an update():\n> ".implode("\n> ", $missing)."\n");
        }

        /** @var self $endpoint */
        $endpoint = self::patch($data, [ "id" => $this->getId() ]);

        return $endpoint;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends a HTTP PATCH Request on behalf of the Endpoint, given optional parameters and a suffix, if desired.
     *
     * @param Endpoint|null $data The data to be passed in the body of the request, can be NULL for EXEC endpoints.
     * @param array $params An optional set of parameters to be interpolated into the URL, when placeholders are used.
     * @param string $suffix An optional suffix to append to the end of the Endpoint URL.
     * @return Endpoint|null Returns the newly patched Endpoint, or NULL in the case of failure or no data required.
     *
     * @throws \Exception
     */
    public static function patch(?Endpoint $data, array $params = [], string $suffix = ""): ?Endpoint
    {
        // Get a reference to the type of Endpoint calling this function.
        $class = get_called_class();

        // Instantiate an AnnotationReader for this class AND check for the important parameters.
        $annotations = new AnnotationReader($class);
        $endpoints = $annotations->getClassAnnotation("endpoints");

        $endpoint = array_key_exists("patch", $endpoints) ? $endpoints["patch"] : null;

        if($endpoint === null || $endpoint === [])
            throw new \Exception("[MVQN\REST\Endpoints\Endpoint] An annotation like '@endpoints { \"patch\": \"/examples/:id\" }' on the ".
                "'$class' class must be declared in order to resolve this endpoint'");

        // Interpolate the URL patterns against any provided parameters.
        $endpoint = Patterns::interpolateUrl($endpoints["patch"], $params);

        // Append any provided suffixes to the URL.
        if($suffix !== "")
            $endpoint .= $suffix;

        // Get an array of all Model properties, with any that do not belong in the PATCH method removed!
        $data = ($data !== null) ? $data->toArray("patch") : [];

        // Attempt to PATCH the specified Endpoint.
        $response = RestClient::patch($endpoint, $data);

        // IF the response is empty, something went VERY wrong!
        if($response === [])
        {
            throw new \Exception("WTF???");
            //return [];
        }

        // HANDLE ANY ERROR CODES HERE...
        if(array_key_exists("code", $response))
        {
            switch($response["code"])
            {
                case 401: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The REST Client was not authorized to make this request!");
                case 403: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] The provided App Key does not have sufficient privileges!");
                case 404: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Endpoint '$endpoint' was not found for class '$class'!");
                case 422: throw new \Exception("[MVQN\REST\Endpoints\Endpoint] Data for endpoint '$endpoint' was improperly formatted!\n".
                    $response["message"]."\n".
                    json_encode($response["errors"], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );

                // TODO: Add other response codes, as they are encountered!

                default:  break; // Likely the key "code" from an actual Endpoint!
            }
        }

        // Finally, return the instantiated Endpoint!
        return new $class($response);
    }

}
