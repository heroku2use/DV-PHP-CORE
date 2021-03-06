<?php

namespace Devless\Schema;

use App\Helpers\Helper;

trait queryParamList
{
    //be sure to update this array when you add a new method else it wont be accessible to the user
    public $query_params = [
        'orderBy' => 'orderBy',
        'asc' => 'asc',
        'desc' => 'desc',
        'size' => 'size',
        'where' => 'where',
        'orWhere' => 'orWhere',
        'offset' => 'offset',
        'related' => 'related',
        'search' => 'search',
        'randomize' => 'randomize',
        'between' => 'between',
        'greaterThan' => 'greaterThan',
        'lessThan' => 'lessThan',
        'lessThanEqual' => 'lessThanEqual',
        'greaterThanEqual' => 'greaterThanEqual',
        'notEqual' => 'notEqual',
    ];

    private function size(&$complete_query, &$payload, &$size_count)
    {
        $complete_query = $complete_query
            . '->take(' . $payload['params']['size'][0] . ')';
        $size_count = $payload['params']['size'][0];
    }

    private function offset(&$complete_query, &$payload)
    {
        $complete_query =
            $complete_query . '->skip(' . $payload['params']['offset'][0] . ')';
    }

    private function randomize(&$complete_query, &$payload)
    {
        $complete_query = $complete_query
            . '->orderByRaw("RAND()")';
    }

    private function related(&$complete_query, &$payload, $table_name, &$related_fetch)
    {
        $related_set = true;
        $service_name = $payload['service_name'];
        $queried_table_list = $payload['params']['related'];

        $related_fetch = function ($results) use ($queried_table_list, $service_name, $table_name, $payload) {
            return $this->_get_related_data(
                $payload,
                $results,
                $table_name,
                $queried_table_list
            );
        };
    }

    private function search(&$complete_query, &$payload)
    {
        $split_query = explode(',', $payload['params']['search'][0]);
        $search_key = $split_query[0];
        $search_words = explode(' ', $split_query[1]);
        foreach ($search_words as $search_word) {
            $complete_query = $complete_query . '->orWhere("' . $search_key . '","ILIKE","%' . $search_word . '%")';
        }
    }

    private function between(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['between'][0]);
        $complete_query = $complete_query
            . '->whereBetween("' . $params[0] . '",[' . $params[1] . ',' . $params[2] . ' ])';
    }

    private function greaterThan(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['greaterThan'][0]);
        $complete_query = $complete_query
            . '->where("' . $params[0] . '",">","' . $params[1] . '")';
    }

    private function greaterThanEqual(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['greaterThanEqual'][0]);
        $complete_query = $complete_query
            . '->where("' . $params[0] . '",">=","' . $params[1] . '")';
    }
    private function lessThan(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['lessThan'][0]);
        $complete_query = $complete_query
            . '->where("' . $params[0] . '","<","' . $params[1] . '")';
    }

    private function lessThanEqual(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['lessThanEqual'][0]);
        $complete_query = $complete_query
            . '->where("' . $params[0] . '","<=","' . $params[1] . '")';
    }

    private function notEqual(&$complete_query, &$payload)
    {
        $params = explode(',', $payload['params']['notEqual'][0]);
        $complete_query = $complete_query
            . '->where("' . $params[0] . '","!=","' . $params[1] . '")';
    }

    private function orderBy(&$complete_query, &$payload)
    {
        $complete_query = $complete_query
            . '->orderBy("' . $payload['params']['orderBy'][0] . '" )';
    }

    private function asc(&$complete_query, &$payload)
    {
        $complete_query = $complete_query
            . '->orderBy("' . $payload['params']['asc'][0] . '", "asc" )';
    }

    private function desc(&$complete_query, &$payload)
    {
        $complete_query = $complete_query
            . '->orderBy("' . $payload['params']['desc'][0] . '", "desc" )';
    }

    private function where(&$complete_query, $payload)
    {
        $this->where_and_orWhere_builder('where', $complete_query, $payload);
    }

    private function orWhere(&$complete_query, $payload)
    {
        $this->where_and_orWhere_builder('orWhere', $complete_query, $payload);
    }

    private function where_and_orWhere_builder($whereType, &$complete_query, $payload)
    {
        foreach ($payload['params'][$whereType] as $one) {
            $query_params = explode(',', $one);
            if (isset($query_params[1], $query_params[0])) {
                $complete_query = $complete_query .
                '->' . $this->query_params[$whereType] . '("' . $query_params[0] .
                    '","' . $query_params[1] . '")';
            } else {
                Helper::interrupt(612);
            }
        }
    }
}
