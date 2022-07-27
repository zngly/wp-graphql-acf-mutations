[![Latest Stable Version](http://poser.pugx.org/zngly/wp-graphql-acf-mutations/v)](https://packagist.org/packages/zngly/wp-graphql-acf-mutations) [![Total Downloads](http://poser.pugx.org/zngly/wp-graphql-acf-mutations/downloads)](https://packagist.org/packages/zngly/wp-graphql-acf-mutations) [![Latest Unstable Version](http://poser.pugx.org/zngly/wp-graphql-acf-mutations/v/unstable)](https://packagist.org/packages/zngly/wp-graphql-acf-mutations) [![License](http://poser.pugx.org/zngly/wp-graphql-acf-mutations/license)](https://packagist.org/packages/zngly/wp-graphql-acf-mutations) [![PHP Version Require](http://poser.pugx.org/zngly/wp-graphql-acf-mutations/require/php)](https://packagist.org/packages/zngly/wp-graphql-acf-mutations)

## Zngly - WpGraphql ACF Mutations

Wordpress plugin which add mutations to ACF Fields

### Install

https://packagist.org/packages/zngly/wp-graphql-acf-mutations

`composer require zngly/wp-graphql-acf-mutations`

### Usage

Install and Activate plugin

Acf fields should show up in mutations

### Specify Graphql Type

If an acf type needs a specific graphql type such as a custom enum then specify
`"strict_graphql_type" => "MyCustomEnum"` in your acf config.

![image](https://user-images.githubusercontent.com/87081580/180242118-a887435c-d665-44aa-b569-c50cc27542c6.png)

### Limitations

Currently the mutations will not be set for objects nested with more than one child deep.

### Dev Usage

-   clone the repository
-   make sure you have composer and npm installed on your system
-   make sure you have XAMPP or something similar installed on your system
-   this will install wordpress along with the plugin

-   `composer install:dev`
-   `composer watch`

-   goto your wordpress installation in your localhost browser
-   finish the wordpress installation
