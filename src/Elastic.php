<?php

namespace PHPDots\ElasticSearchTailor;

use Elasticsearch\ClientBuilder;

class Elastic
{
	public function __construct()
	{
		$hosts = config('elasticConfig.hosts');
		$this->client = ClientBuilder::create()->setHosts($hosts)->setRetries(2)->build();
	}

	public function createIndex($name = 'default', $shards = 1, $replicas = 1)
	{
		$index['index'] = $name;
		$index['body']['number_of_shards'] = $shards;
		$index['body']['number_of_replicas'] = $replicas;

		return $this->client->indices()->create($index);
	}

	public function putSettings($index = 'default', $mappings, $type)
	{
		$params['index'] = $index;
		$params['type'] = $type;
		$params['body'][$type]['properties'] = $mappings;
		$params['body'][$type]['_source'] = [
			'enabled' => true
		];

		return $this->client->indices()->putMapping($params);
	}

	public function deleteIndex($index)
	{
		$params['index'] = $index;

		return $this->client->indices()->delete($params);
	}

	public function searchAll($mark_text,$page = 1, $length = 10, $exact = 0, $sort= 0)
	{
		/*$start_date = strtotime($start_date);
		$last_date = strtotime($last_date);
		$start_date = date('Ymd', $start_date);
		$last_date = date('Ymd', $last_date);*/
		$from = $page * $length;
		if (($from+$length) > 10000) {
			throw new Exception("Search Out of Bound, $page * $length should not be greater or equal to 10,000. ", 1);
			exit();
		}
		if (empty($mark_text)) {
			throw new Exception("Mark Text Should Not Be Null", 1);
			exit();
		}
		$query = [];
		$query['index'] = "uspto,euipo";
		$query['from'] = $from;
		$query['size'] = $length;
		if ($sort) {
			$query['body']['sort']['did']['order'] = 'desc';
		}

		if ($exact) {
			$query['body']['query']['bool']['should'][]['match'] = [
				'mark_identification' => $mark_text
			];
			$query['body']['query']['bool']['should'][]['match'] = [
				'mark_text' => $mark_text
			];
		} else {
			$query['body']['query']['bool']['should'][]['query_string'] = [
				'query' => "*$mark_text*",
				'default_field' => "mark_identification"
			];
			$query['body']['query']['bool']['should'][]['query_string'] = [
				'query' => "*$mark_text*",
				'default_field' => "mark_text"
			];
		}

		/*if (!empty($start_date))
		{
			$query['body']['query']['bool']['must'][]['range']['filing_date'] = [
				'gte' => $start_date,
				'lte' => $last_date
			];
		}*/

		/*if (!empty($owner_name))
		{
			$query['body']['query']['bool']['must'][] = [
				'wildcard' => [
					'party_name' =>  '*'.$owner_name.'*',
				]
			];
		}*/

		try {
			$result = $this->client->search($query);
			return ['total' => $result['hits']['total'], 'records' => $result['hits']['hits']];
		} catch (\Exception $e) {
			return [];
		}
	}

	public function search($index, $mark_text, $page = 1, $length = 10, $exact = 0, $sort = 0)
	{
		/*$start_date = strtotime($start_date);
		$last_date = strtotime($last_date);
		$start_date = date('Ymd', $start_date);
		$last_date = date('Ymd', $last_date);*/
		$from = ($page - 1) * $length;
		if (($from+$length) > 10000) {
			throw new Exception("Search Out of Bound, page * length should not be greater than 10,000. ", 1);
			exit();
		}
		if (empty($mark_text)) {
			throw new Exception("Mark Text Should Not Be Null", 1);
			exit();
		}
		$query = [];
		$query['index'] = $index;
		$query['from'] = $from;
		$query['size'] = $length;
		if ($sort) {
			$query['body']['sort'][]['did']['order'] = 'desc';
		}

		if ($index == 'uspto') {
			if (!$exact) {
				$query['body']['query']['bool']['must'][]['query_string'] = [
					'query' => "*$mark_text*",
					'default_field' => "mark_identification"
				];
			} else {
				$query['body']['query']['bool']['must'][]['match'] = [
					'mark_identification' => $mark_text
				];
			}
		}
		if ($index == "euipo") {
			if (!$exact) {
				$query['body']['query']['bool']['must'][]['query_string'] = [
					'query' => "*$mark_text*",
					'default_field' => "mark_text"
				];
			} else {
				$query['body']['query']['bool']['must'][]['match'] = [
					'mark_text' => $mark_text
				];
			}
		}

		try {
			$result = $this->client->search($query);
			return ['total' => $result['hits']['total'], 'records' => $result['hits']['hits']];
		} catch (\Exception $e) {
			return [];
		}
	}
}
