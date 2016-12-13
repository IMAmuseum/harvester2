##Harvester Package

###Composer Setup
```json
    "require": {
        "imamuseum/harvester2": "dev-master"
    },
```

### Service Provider
In `config/app.php` add to the autoloaded providers -
```php
Imamuseum\Harvester\HarvesterServiceProvider::class,
```

Add ExampleHarvester to `app/Providers/AppServiceProvider.php` to implement the HarvesterInterface.
```php
    public function register()
    {
        $this->app->bind('Imamuseum\Harvester\Contracts\HarvesterInterface',
            'Imamuseum\Harvester\ExampleHarvester');
    }
```

Now you can publish the package -
```sh
php artisan vendor:publish

```

Run Migrations -
```sh
php artisan migrate
```

Run to pull all objects and their relationships (relationships can be across multiple sources so its best to pull objects from all sources first and then pull relationships from all sources)
```sh
php artisan harvest:collection --all --source=your_db_source
php artisan harvest:collection --relate source=your_db_source
```

Push items off the queue -
```sh
php artisan queue:listen
```

### Artisan Commands
update all objects in source that have changed since given time period in config
```sh
php artisan harvest:collection --source=your_db_source
php artisan harvest:collection --source=your_db_source
```
Use the --help flag after any command to view the available options with a description.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).