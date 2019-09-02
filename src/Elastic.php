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

	public function singleDataIndex($index, $type, $body)
	{
		$params['body'] = $body;
		$params['type'] = $type;
		$params['index'] = $index;

		if (empty($body)) {
			throw new Exception("Empty array for indexing", 1);
			exit();
		}

		$ret = $this->client->index($params);
		return $ret;
	}

	public function bulkDataIndex($index, $type, $body)
	{
		$respones = [];
		if (!empty($body)) {
			for ($i = 0; $i < count($body); $i++) {
				$params['body'][] = [
					'index' => [
						'_index' => $index,
						'_type' => $type,
					]
				];

				$params['body'][] = $body[$i];

				if ($i % 1000) {
					$respones = $this->client->bulk($params);
					$params = [];
				}
			}
		} else {
			throw new Exception("Empty array for Indexing", 1);
			die();
		}

		return $respones;
	}

	public function search($index, $mark_text, $type = '', $exact = 0)
	{
		/*$start_date = strtotime($start_date);
		$last_date = strtotime($last_date);
		$start_date = date('Ymd', $start_date);
		$last_date = date('Ymd', $last_date);*/
		$query = [];
		$query['index'] = $index;
		if (!empty($type)) {
			$query['type'] = $type;
		}

		if ($index == 'uspto') {
			if (!$exact) {
				if (!empty($mark_text)) {
					$query['body']['query']['bool']['must'][]['query_string'] = [
						'query' => "\"$mark_text\"",
						'default_fields' => "mark_identification"
					];
				}
			} else {
				if (!empty($mark_text)) {
					$query['body']['query']['bool']['must'][]['match'] = [
						'mark_identification' => $mark_text
					];
				}
			}

			/*if (!empty($start_date))
			{
				$query['body']['query']['bool']['must'][]['range']['filing_date'] = [
					'gte' => $start_date,
					'lte' => $last_date
				];
			}*/
		}
		if ($index == "euipo") {
			if (!$exact) {
				if (!empty($mark_text)) {
					$query['body']['query']['bool']['must'][]['query_string'] = [
						'query' => "\"*$mark_text*\"",
						'default_fields' => "mark_text"
					];
				}
			} else {
				if (!empty($mark_text)) {
					$query['body']['query']['bool']['must'][]['match'] = [
						'mark_text' => "\"*$mark_text*\""
					];
				}
			}
		}

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
			return $result['hits']['hits'];
		} catch (\Exception $e) {
			return [];
		}
	}
}
