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
To create a new index in elasrticseearch, we have a function `createIndex` . `createIndex($name='default', $shards=1, $replicas=1)`. It gives acknowledgement from the elasticsearch.

For example : 
```php
$result= $this->elastic->createIndex($index, 1, 1);
```

To put mappings into a  type/table in elasticsearch, there is a function `putSettings`. `putSettings($index='default', $mappings, $type)`. It also gives acknowledgement from elasticsearch.
`$mappings` must be an array of valid settings.
For example :
```php
$result = $this->elastic->putSettings('uspto', $mapping, 'case_file');
```

To index documents/records, we have a function `bulkIndex`. `bulkIndex($index, $indexType, $start_range)`.
`$index` is index name in which data is going to be indexed. `$indexType` is  `full` or `partial`. In full, all the records are going to be indexed.In full, previously indexed records will be updated. In partial, indexing will start from last indexed record. Only new records are going to be indexed here.


Right now, we have too much data to index, so we have decided not to index all the data in single function call, we can pass `$start_range` , 1,000,000 records after that will be indexed.

For example : 
```php
\\To index data after 9 million records.
$this->elastic->bulkIndex('uspto', 'full', 9000000);
```





























