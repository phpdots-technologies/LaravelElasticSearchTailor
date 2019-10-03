# LaravelElasticSearchTailor
Data search via Elastic Search Package

# Installation

Install this package by this command : 
```bash
composer require phpdots/elasticsearchtailor
```

The service provider will be automatically registered using package discovery.

If you don't use auto-discovery you should add the service provider to the providers array in `config/app.php`.

```php
// existing providers...
PHPDots\ElasticSearchTailor\ElasticSearchTailorServiceProvider::class,
```

Once the package is installed you should publish the configuration.

```shell
php artisan vendor:publish --provider="PHPDots\ElasticSearchTailor\ElasticSearchTailorServiceProvider"
```

You can set your nodes in the config file `config\elasticcConfig.php`.

```php
<?php 
// Your nodes resides here.
return array(
    'hosts' => ['localhost:8000'],
);
?>
```

# Usage

Fist , you need to import our class in your controller or model. 

```php
use PHPDots\ElasticSearchTailor\Elastic;
```

Then you need to create an instance of the class. Like:

```php
$elastic = new Elastic();
```

Now, you are ready to use our functions.
To create a new index in elasrticseearch, we have a function `createIndex` . 

`createIndex($name='default', $shards=1, $replicas=1)`. 

It gives acknowledgement from the elasticsearch.

For example : 
```php
$result= $this->elastic->createIndex($index, 1, 1);
```

To put mappings into a  type/table in elasticsearch, there is a function `putSettings`. 

`putSettings($index='default', $mappings, $type)`. 

It also gives acknowledgement from elasticsearch. `$mappings` must be an array of valid settings.


For example :
```php
$result = $this->elastic->putSettings($index, $mapping, $type);
```

For the Search, we have just implemented search on some fields which we can improve later. 

`search($index,$mark_text,$page = 1, $length = 10, $exact = 0, $sort= 0)`. 

Here we have `$mark_text` for searching keyword and `$index` for searching in particular index. 
We have also added `$page` and `$length` for pagination. For example, if $page=1 and $length=20, result will give you 20 records starting from 0.
We have `$exact`, if want to use the exact word. 
We have `$sort`, if want to sort the result as per id descending.
 
 
For example : 
```php
$result = $this->elastic->search('uspto','mark_text', 1, 10, 1, 1);
```


This is a sample return object: 
```php
{
  "took" : 67,
  "timed_out" : false,
  "_shards" : {
    "total" : 2,
    "successful" : 2,
    "skipped" : 0,
    "failed" : 0
  },
  "hits" : {
    "total" : 4582,
    "max_score" : null,
    "hits" : [
      {
        "_index" : "uspto",
        "_type" : "case_file",
        "_id" : "9434cb7b902f26f8b56aacb92bf42e74",
        "_score" : null,
        "_source" : {
          "id" : "9434cb7b902f26f8b56aacb92bf42e74",
          "did" : 23120213,
          "mark_identification" : "ECHOS CALLING",
          "serial_number" : "88402444.00",
          "registration_number" : "0000000",
          "filing_date" : 20190425,
          "registration_date" : null,
          "status_code" : "630",
          "primary_code" : "033",
          "case_file_owners_id" : 22640023,
          "party_name" : "Ste. Michelle Wine Estates Ltd."
        },
        "sort" : [
          23120213
        ]
      },...
```

If there is any error `search` function will return only `null array`.

We have also added a function for searching in All indices.

`searchAll($mark_text,$page = 1, $length = 10, $exact = 0, $sort= 0)`.

It works same as `search` function but gives records from all indices.

We hope it helps you.


























