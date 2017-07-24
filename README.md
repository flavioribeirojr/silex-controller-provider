# silex-controller-provider

Controller provider for silex

# What is this?

This provider contains the logic to load your controllers dynamically, registering them in you Silex Applications. With this, you
no longer needs to manually register each one of your controllers, passing their respective dependencies.

# Usage

To install it:

```sh
$ composer require flaviojr/silex-controller-provider
```

To use this provider, you simply need to register it within your application:

```php
//Don't forget to register this provider first
$app->register(new Silex\Provider\ServiceControllerServiceProvider);

$app->register(new Sneek\Providers\ControllerProvider('controller-dir', 'Your-root-namespace'[, 'your-namespace-mirror']));
```

* The first parameter indicate where your **controller files** are.
* The second one is the root namespace from where your controllers are. For example, if your controller namespace is
  `App\Controllers\HomeController`, then the value to be passed is the string 'App'.
* The last parameter is optional, you should use it if your root namespace isn't the name of your root directory. For example,
  if you have a folder structure like this: `src/Controllers/CoffeeController`, having the namespace `App\Controllers\CoffeeController`
  you have to pass the 'src' string, so that the provider know that 'src' is equivalent to 'App' in this case.
  
  
# Using the registered controllers

The provider register your controllers using their namespaces as index. To access they in your routes, you just need to pass
their namespace followed by the method you are trying to access.

```php
$app->get('/', 'App\Controllers\CoffeeController:index');
```

# Notes

* You don't have to worry about your controller dependencies, the provider will recursively resolve each one of them.
