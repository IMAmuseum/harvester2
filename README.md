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

Run Migrations -
```sh
php artisan migrate
```

Push items off the queue -
```sh
php artisan queue:listen
```

### Artisan Commands
```sh
php artisan harvest - Runs sync
```
Use the --help flag after any command to view the available options with a description.

### License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
