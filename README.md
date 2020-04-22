# Yamoah

Yamoah is a php library for WordPress theme and plugin developers. It allows the developer to easily create custom data tables in the database that the WordPress users can interact with using the WordPress admin dashboard UI.

### Getting Started

Follow the instructions below to get started using Yamoah in your WordPress project.

### Prerequisites

You must have composer installed - [click here to install](https://getcomposer.org/download/) and [WordPress](https://github.com/WordPress/WordPress)

### Installing

We recommend installing this library in the `includes` directory of your WordPress theme / plugin. If you do not already have an `includes` directory in your theme / plugin, you can create one with the following command:

```
mkdir includes
```

Then, simply clone this repository into the new, `includes` directory:

```
cd includes
git clone https://github.com/morgbillingsley/Yamoah.git
```

Once the git repository has successfully cloned into the includes directory, make sure all of the composer dependencies are installed:

```
cd Yamoah
composer install
```

### Usage

To use the Yamoah library, include the init.php file:

```php

require_once __DIR__ . "/includes/Yamoah/init.php"

\Yamoah\Table::create([
    "name" => "articles",
    "schema" => [
        "title" => "string",
        "author" => "string",
        "body" => "text",
        "image" => "media"
    ]
]);

```