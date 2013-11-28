<?php
namespace Celd\ProfileGui\Repository;
use Elastica\Client;
use Elastica\Request;
use Elastica\Query\Builder;
use Elastica\Query;

class Profiles {
    protected $client, $index, $type;
    protected $typeName = 'profiles';
    protected $defaultFields = '["host", "uri", "mu", "pmu", "cpu", "wt", "tstamp"]';

    public function __construct() {
        $this->client = new Client(array('host' => '127.0.0.1'));
        $this->index = $this->client->getIndex('profileguiv1');
        if(!$this->index->exists()) {
            $this->index->create(array(), true);
            $this->createMapping();
        }

        $this->type = $this->index->getType($this->typeName);

        return $this;
    }

    protected function createMapping() {
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($this->index->getType($this->typeName));

        // Set mapping
        $mapping->setProperties(array(
                'profile'    => array(
                    'type' => 'nested',
                    'properties' => array(
                        'main()'      => array(
                            'type' => 'nested'
                        )
                    ),
                    "include_in_all" => false,
                    "index" => "not_analyzed"
                ),
                'host'     => array('type' => 'string', 'include_in_all' => TRUE),
                'uri'     => array('type' => 'string', 'include_in_all' => TRUE),
                'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE, 'store' => 'yes'),
                'wt'     => array('type' => 'integer', 'include_in_all' => TRUE),
                'cpu'     => array('type' => 'integer', 'include_in_all' => TRUE),
                'mu'     => array('type' => 'integer', 'include_in_all' => TRUE),
                'pmu'     => array('type' => 'integer', 'include_in_all' => TRUE),
            ));

        // Send mapping to type
        $mapping->send();
    }

    public function search($term) {
        return $this->type->search($term);
    }

    public function addDocument($doc) {
        $this->type->addDocument($doc);
    }

    public function refreshIndex() {
        $this->index->refresh();
    }

    public function getLatest($limit = 25) {
        $query = '{
            "from" : 0,
            "size" : ' . $limit . ',
            "fields": ' . $this->defaultFields . ',
            "sort": [
                {
                    "tstamp": { "order": "desc" }
                }
            ]
        }';
        return $this->getResponseArray($query);
    }

    public function getLatestWithFilter($field, $value, $limit=25) {
        $query = '{
            "from" : 0,
            "size" : ' . $limit . ',
            "fields": ' . $this->defaultFields . ',
            "sort": [
                {
                    "tstamp": { "order": "desc" }
                }
            ],
            "query" : {
                "bool" : {
                    "must" : {
                        "term" : { "'.$field.'" : "'. $value . '" }
                    }
                }
            }
        }';
        return $this->getResponseArray($query);
    }

    public function getById($id) {
        $query = '{
            "query" : {
                "bool" : {
                    "must" : {
                        "term" : { "_id" : "'. $id . '" }
                    }
                }
            }
        }';
        return $this->getResponseArray($query);
    }

    public function getAverage() {
        $query = '
               {
         "from" : 0,
         "size" : 0,
    "query" : {
        "match_all" : {}
    },
    "facets" : {
        "histogram" : {
            "histogram" : {
                "key_field" : "tstamp",
                "value_field" : "wt",
                "time_interval" : "1d"
            }
        }
    }
}
        ';
        $wt = $this->getResponseArray($query);

        $query = '
               {
         "from" : 0,
         "size" : 0,
    "query" : {
        "match_all" : {}
    },
    "facets" : {
        "histogram" : {
            "histogram" : {
                "key_field" : "tstamp",
                "value_field" : "wt",
                "time_interval" : "1d"
            }
        }
    }
}
        ';
        $mu = $this->getResponseArray($query);

        return array('wt' => $wt, 'mu' => $mu);
    }

    protected function getResponseArray($query) {
        $response = $this->client->request($this->getPath(), Request::GET, $query);
        $responseArray = $response->getData();
        return $responseArray;
    }

    protected function getPath() {
        return $this->index->getName() . '/' . $this->type->getName() . '/_search';
    }

    public function getForHost($host) {

    }

}