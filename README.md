# Ibexa Commerce eContent data installer

This package contains eContent sample data installer for testing purposes.

## Installation

1. Install the package
    ```
    composer require ezsystems/ezcommerce-econtent-installer
    ```

1. Enable the bundle in your `config/bundles.php`:
    ```php
    Ibexa\Platform\Bundle\Commerce\EContentInstaller\IbexaPlatformCommerceEContentInstallerBundle::class => ['all' => true],    
    ```
   
## Usage

To install eContent demo data use the following Symfony command:

```
php bin/console ezplatform:install commerce-econtent-demo
``` 

## COPYRIGHT
Copyright (C) 1999-2020 Ibexa AS. All rights reserved.

## LICENSE
- https://www.ibexa.co/software-information/licenses-and-agreements/ibexa-trial-and-test-license-agreement-ibexa-ttl-v2.2 Ibexa Business Use License Agreement Ibexa BUL Version 2.3
- https://www.ibexa.co/software-information/licenses-and-agreements/ibexa-business-use-license-agreement-ibexa-bul-version-2.3 Ibexa Trial and Test License Agreement (Ibexa TTL) v2.2
