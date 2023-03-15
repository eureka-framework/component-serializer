# component-serializer
[![Current version](https://img.shields.io/packagist/v/eureka/component-serializer.svg?logo=composer)](https://packagist.org/packages/eureka/component-serializer)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=7.4%20-%208.2&color=777bb4)](https://packagist.org/packages/eureka/component-serializer)
![CI](https://github.com/eureka-framework/component-serializer/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-serializer&metric=alert_status)](https://sonarcloud.io/dashboard?id=eureka-framework_component-serializer)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-serializer&metric=coverage)](https://sonarcloud.io/dashboard?id=eureka-framework_component-serializer)

## Why?

Component to serialize & deserialize object, mainly use by client SDK to (de)serialize VO for cache


## Installation

If you wish to install it in your project, require it via composer:

```bash
composer require eureka/component-serializer
```



## Usage

### Simple (un)serialization
This library will provide a (un)serializer service to allow safe caching of value objects.

```php
<?php
namespace Application;

use Application\VO\AnyObject;
use Eureka\Component\Serializer\JsonSerializer;

$serializer = new JsonSerializer();

$originalVO = new AnyObject(1, 'name', 'any arg');

//~ Serialize a VO into json string
$json = $serializer->serialize($originalVO);

//~ Unserialize a serialized VO
$unserializedVO = $serializer->unserialize($json, Application\VO\AnyObject::class);
```

To allow correct serialization & unserialization, the VO must implement `\JsonSerializable` interface and use
the provided `JsonSerializableTrait`. This trait handle automatically the sub VO for the (un)serialization.

For a list off sub VO (collection), a collection object must be provided in constructor.

### Complex (un)serialization with collection

First, you need to have a collection object. We provide an AbstractCollection class to manage all base operation.

So, create a Collection class:
```php
<?php

declare(strict_types=1);

namespace Application\VO;

use Eureka\Component\Serializer\Exception\CollectionException;
use Eureka\Component\Serializer\VO\AbstractCollection;

class CollectionEntityB extends AbstractCollection implements \JsonSerializable
{
    /**
     * Class constructor.
     *
     * @param array
     */
    public function __construct(array $dataEntitiesB)
    {
        foreach ($dataEntitiesB as $dataEntityB) {
            $this->add(new EntityB($dataEntityB['id'], $dataEntityB['name']));
        }
    }

    /**
     * Override parent method to ensure we have only required sub VO entity. 
     * 
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws CollectionException
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof EntityB) {
            throw new CollectionException('Data must be an instance of ' . EntityB::class);
        }

        parent::offsetSet($offset, $value);
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Application\VO;

use Eureka\Component\Serializer\JsonSerializableTrait;

class EntityA implements \JsonSerializable
{
    use JsonSerializableTrait;

    private int $id;
    private string $name;
    private ?CollectionEntityB $listEntitiesB;

    /**
     * EntityA constructor.
     *
     * @param int $id
     * @param string $name
     * @param CollectionEntityB|null $listEntitiesB
     */
    public function __construct(
        int $id,
        string $name,
        ?CollectionEntityB $listEntitiesB = null
    ) {
        $this->id            = $id;
        $this->name          = $name;
        $this->listEntitiesB = $listEntitiesB;
    }
    
    //...
}
```

```php
<?php

declare(strict_types=1);

namespace Application\VO;

use Eureka\Component\Serializer\JsonSerializableTrait;

class EntityB implements \JsonSerializable
{
    use JsonSerializableTrait;

    private int $id;
    private string $name;

    public function __construct(
        int $id,
        string $name
    ) {
        $this->id   = $id;
        $this->name = $name;
    }
    
    //...
}
```


Now, try to (un)serialize VO

```php
<?php
namespace Application;

use Application\VO\CollectionEntityB;
use Application\VO\EntityA;
use Application\VO\EntityB;
use Eureka\Component\Serializer\JsonSerializer;

$serializer = new JsonSerializer();

$dataList = [
    ['id' => 1, 'name B#1'],
    ['id' => 2, 'name B#2'],
];
$originalVO = new EntityA(1, 'name', new CollectionEntityB($dataList));

//~ Serialize a VO into json string
$json = $serializer->serialize($originalVO);

//~ Unserialize a serialized VO
$unserializedVO = $serializer->unserialize($json, Application\VO\EntityA::class);

//~ Manipulate collection from unserialized entity
foreach ($unserializedVO->getCollectionEntityB() as $entityB) {
    echo $entityB->getName() . PHP_EOL;
}
```


## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.


### Install / update project

You can install project with the following command:
```bash
make install
```

And update with the following command:
```bash
make update
```

NB: For the components, the `composer.lock` file is not committed.

### Testing & CI (Continuous Integration)

#### Tests
You can run tests (with coverage) on your side with following command:
```bash
make tests
```

For prettier output (but without coverage), you can use the following command:
```bash
make testdox # run tests without coverage reports but with prettified output
```

#### Code Style
You also can run code style check with following commands:
```bash
make phpcs
```

You also can run code style fixes with following commands:
```bash
make phpcbf
```

#### Static Analysis
To perform a static analyze of your code (with phpstan, lvl 9 at default), you can use the following command:
```bash
make analyze
```

Minimal supported version:
```bash
make php74compatibility
```

Maximal supported version:
```bash
make php82compatibility
```

#### CI Simulation
And the last "helper" commands, you can run before commit and push, is:
```bash
make ci  
```


## License

This project is licensed under the MIT License - see the `LICENSE` file for details
