
## Installation

Install challenge-project

```bash
  composer update
  php artisan migrate --seed
  php artisan serve
```

## Running Tests

To run tests, run the following command

```bash
  php artisan test
```


# The Challenge project

in order to optimize the perfomance i have added some caching process , in services and two schedules running bellow commands:
```bash
  php artisan heavenlyTour:cacheAvailableProducts
  php artisan heavenlyTour:cacheProductsInfo
```


