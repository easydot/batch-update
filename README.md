# Update Batch
Update batch rows in one query!

### Support
`laravel`

### Install
```
composer require easydot/batch-update
```

Base Usage
```php
(new UpdateBatch('table_name', $batchArrayData))->run();


# Specify primary key
(new UpdateBatch('table_name', $batchArrayData, 'primary_key'))->run();

# Add filter where
(new UpdateBatch('table_name', $batchArrayData, 'primary_key'))
    ->where('type', '=', $this->tmpOfferType)
    ->run();

# Support where
(new UpdateBatch('table_name', $batchArrayData, 'primary_key'))
    ->where('type', '=', $this->tmpOfferType)
    ->run();
    
# Support whereIn
(new UpdateBatch('table_name', $batchArrayData, 'primary_key'))
    ->whereIn('id', [1, 2, 3])
    ->run();
    
# Another methods
(new UpdateBatch('table_name'))
    ->multipleData($batchDataToUpdate)
    ->referenceColumn('primary_id')
    ->run();
```