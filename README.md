##Harvester Package

###Composer Setup
```json
    "require": {
        "imamuseum/harvester2": ""
    },
```

### Service Provider
In `config/app.php` add to the autoloaded providers -
```php
Imamuseum\Harvester2\HarvesterServiceProvider::class,
```

Add ExampleHarvester to `app/Providers/AppServiceProvider.php` to implement the HarvesterInterface.
```php
    public function register()
    {
        $this->app->bind('Imamuseum\Harvester2\Contracts\HarvesterInterface',
            'Imamuseum\Harvester2\ExampleHarvester');
    }
```

Now you can publish the package -
```sh
php artisan vendor:publish

```

Push items off the queue
```sh
php artisan queue:listen
```

Be sure to configure your queue and create a failed jobs table in your database.
I suggest using an sqlite database if you are only going to use it for the failed jobs table.
https://laravel.com/docs/master/queues

### Artisan Commands
```sh
php artisan create-index --index=optional - Re-Creates document Store indices according to config. Option to specify which index.
php artisan harvest --source=optional - Runs sync. Option to specify source
```
Use the --help flag after any command to view the available options with a description.

### License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

