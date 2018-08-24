<?php

use MVQN\Collections\{Collection, CollectionException};
use Tests\MVQN\Collections\Examples\{Country, State};

class CollectionTests extends PHPUnit\Framework\TestCase
{
    /** @var array $countries */
    protected $countries;

    protected function setUp()
    {
        $countryP1 = new Country([ "name" => "Test+1", "code" => "+1" ]);
        $countryP2 = new Country([ "name" => "Test+2", "code" => "+2" ]);
        $countryP3 = new Country([ "name" => "Test+3", "code" => "+3" ]);
        $countryP4 = new Country([ "name" => "Test+4", "code" => "+4" ]);

        $this->countries = [ $countryP1, $countryP2, $countryP3, $countryP4 ];
    }



    public function test__construct()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        echo $collection."\n";
        echo "\n";
    }

    public function testIterator()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        echo "[\n";

        foreach($collection as $country)
            echo "\t".$country."\n";

        echo "]\n";
        echo "\n";
    }

    public function testIndexers()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        echo "Collection->at(1)                                  : ".$collection->at(1)."\n";

        echo "Collection->first()                                : ".$collection->first()."\n";
        echo "Collection->last()                                 : ".$collection->last()."\n";

        echo "Collection->all()                                  : ".$collection->all()."\n";

        echo "Collection->every([0,2,3])                         : ".$collection->every([0,2,3])."\n";

        echo "Collection->slice(1,2)                             : ".$collection->slice(1,2)."\n";

        echo "\n";
    }

    public function testAddRemove()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        $countryP5 = new Country(["name" => "Test+5", "code" => "+5"]);
        $countryP6 = new Country(["name" => "Test+6", "code" => "+6"]);
        $countryP7 = new Country(["name" => "Test+7", "code" => "+7"]);

        echo 'Collection->push($countryP5)                       : '.$collection->push($countryP5)."\n";
        echo 'Collection->pushMany([$countryP6,$countryP7])      : '.$collection->pushMany([$countryP6,$countryP7])."\n";

        $countryZ0 = new Country(["name" => "Test 0", "code" => " 0"]);
        $countryN1 = new Country(["name" => "Test-1", "code" => "-1"]);
        $countryN2 = new Country(["name" => "Test-2", "code" => "-2"]);

        echo 'Collection->unshift($countryZ0)                    : '.$collection->unshift($countryZ0)."\n";
        echo 'Collection->unshiftMany([$countryN1,$countryN2])   : '.$collection->unshiftMany([$countryN1,$countryN2])."\n";

        echo 'Collection->remove(2)                              : '.$collection->remove(2)."\n";
        echo 'Collection->removeMany(0,2)                        : '.$collection->removeMany(0,2)."\n";

        echo 'Collection->insert(0, $countryN2)                  : '.$collection->insert(0, $countryN2)."\n";
        echo 'Collection->insertMany(1,[$countryN1,$countryZ0])  : '.$collection->insertMany(1,[$countryN1,$countryZ0])."\n";

        echo "\n";
    }

    public function testPushPop()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        echo 'Collection->pop()                                  : '.$collection->pop()."\n";
        echo '                                                   : '.$collection."\n";
        echo 'Collection->popMany()                              : '.$collection->popMany(2)."\n";
        echo '                                                   : '.$collection."\n";

        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        echo 'Collection->shift()                                : '.$collection->shift()."\n";
        echo '                                                   : '.$collection."\n";
        echo 'Collection->shiftMany(2)                           : '.$collection->shiftMany(2)."\n";
        echo '                                                   : '.$collection."\n";

        echo "\n";
    }

    public function testDelete()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        $countryP1 = new Country([ "name" => "Test+1", "code" => "+1" ]);
        $countryP2 = new Country([ "name" => "Test+2", "code" => "+2" ]);
        //$countryP3 = new Country([ "name" => "Test+3", "code" => "+3" ]);
        $countryP4 = new Country([ "name" => "Test+4", "code" => "+4" ]);

        echo 'Collection->delete($countryP2)                     : '.$collection->delete($countryP2)."\n";
        echo 'Collection->deleteMany([$countryP1,$countryP4])    : '.$collection->deleteMany([$countryP1, $countryP4])."\n";

        echo "\n";
    }

    public function testEach()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        $collection->each(
            function(Country $country)
            {
                $country = new Country([ "name" => $country->getName(), "code" => "~".$country->getCode() ]);
                return $country;
            }
        );

        echo 'Collection->each(...) : '.$collection."\n";

        echo "\n";
    }

    public function testFind()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        $countryP2 = $collection->find(
            function(Country $country)
            {
                return $country->getName() === "Test+2";
            }
        );

        echo $countryP2;

        echo "\n";
    }

    public function testFindWhere()
    {
        $collection = new Collection(Country::class, $this->countries);
        $this->assertCount(4, $collection);

        $countryP5 = new Country(["name" => "Test+5", "code" => "+5"]);
        $countryP6 = new Country(["name" => "Test+6", "code" => "+6"]);
        $countryP7 = new Country(["name" => "Test+7", "code" => "+7"]);

        $collection->pushMany([$countryP5, $countryP6,$countryP7]);

        $countryZ0 = new Country(["name" => "Test 0", "code" => " 0"]);
        $countryN1 = new Country(["name" => "Test-1", "code" => "-1"]);
        $countryN2 = new Country(["name" => "Test-2", "code" => "-2"]);

        $collection->unshiftMany([$countryZ0, $countryN1,$countryN2]);

        echo $collection."\n";

        $positives = $collection->find(
            function(Country $country)
            {
                return strpos($country->getName(), "+") !== false;
            }
        );
        echo $positives."\n";

        $countryP2 = $collection->where("name", "Test+2");
        echo $countryP2."\n";

        $countryP3 = $collection->whereAll([ "name" => "Test+3", "code" => "+3" ]);
        echo $countryP3."\n";

        $negatives = $collection->whereAny([ "name" => "Test-1", "code" => "-2" ]);
        echo $negatives."\n";

        echo "\n";
    }

    public function testCollectionException()
    {
        $this->expectException(CollectionException::class);

        try
        {
            $state = new State([ "countryId" => 1, "name" => "State1", "code" => "S1" ]);
            $collection = new Collection(Country::class, [ $state ]);
        }
        catch(CollectionException $ce)
        {
            echo $ce->getMessage();
            throw $ce;
        }
    }

}
