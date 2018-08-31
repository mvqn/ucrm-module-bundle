<?php

use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\Country;
use UCRM\REST\Endpoints\Client;

class RestClientTests extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        RestClient::setBaseUrl("http://ucrm.dev.mvqn.net/api/v1.0");
        RestClient::setHeaders([
            "Content-Type: application/json",
            "X-Auth-App-Key: j/Zne60F/72FgqC7wL/dYnp2xi554+Pu7n3YD8xGKqsaAasvCv+8V6leqbPX2lWb"
        ]);
    }

    public function testGet()
    {
        //$result = RestClient::get("/version");

        /** @var Country $result */
        //$result = Country::get();
        $result = Country::getByID(249);

        echo $result . "\n";

    }


    public function testValidate()
    {
        //$clients = Client::get();
        $client = new Client();
        $client
            ->setOrganizationId(1)
            ->setIsLead(true)
            ->setClientType(Client::CLIENT_TYPE_COMMERCIAL)
            ->setInvoiceAddressSameAsContact(true)
            ->setRegistrationDate(new DateTime());


        //$inserted = $client->insert();

        if(!$client->validate("patch", $missing))
        {
            print_r($missing);



        }
        else
        {
            $inserted = $client->insert();

            echo $inserted."\n";
        }


        //echo $clients."\n";


    }







}