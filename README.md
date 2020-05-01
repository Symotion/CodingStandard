# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of rules for PHP_CodeSniffer, PHP-CS-Fixer and PHPStan used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/coding-standard --dev
composer require symplify/easy-coding-standard --dev
```

1. Run with ECS:

```bash
vendor/bin/ecs process src --set symplify
```

2. Register rules for PHPStan:

```neon
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/config/symplify-rules.neon
```

## Rules Overview

- Jump to [Object Calisthenics rules](#object-calisthenics-rules)
- Rules with :wrench: are configurable.

<br>

### Cognitive Complexity for Method Must be Less than X

- :wrench:
- [Why it's the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

1) **For ECS**

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff`](packages/coding-standard/src/Sniffs/CleanCode/CognitiveComplexitySniff.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff:
        maxCognitiveComplexity: 8 # default
```

2) **For PHPStan**

- class: [`Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)

```neon
# phpstan.neon
parameters:
    symplify:
        max_cognitive_complexity: 8 # default

rules:
    - Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
```

:x:

```php
<?php

class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                if ($value !== 3) {
                    return false;
                }
            }
        }

        return true;
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        if ($value === 2) {
            return true;
        }

        return $value === 3;
    }
}
```

<br>

### Cognitive complexity for class must be less than X

Same as the one above just for classes.

- :wrench:

1) **For ECS**

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ClassCognitiveComplexitySniff`](packages/coding-standard/src/Sniffs/CleanCode/ClassCognitiveComplexitySniff.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ClassCognitiveComplexitySniff:
        maxClassCognitiveComplexity: 50 # default
```

2) **For PHPStan**

- class: [`Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

```neon
# phpstan.neon
parameters:
    symplify:
        max_class_cognitive_complexity: 50 # default

rules:
    - Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
```

<br>

### Classes with Static Methods must have "Static" in the Name

- [Why is static bad?](https://tomasvotruba.com/blog/2019/04/01/removing-static-there-and-back-again/)
- be honest about static
- value object static constructor methods are excluded
- EventSubscriber and Command classes are excluded

```neon
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\Naming\NoClassWithStaticMethodWithoutStaticNameRule
```

:x:

```php
<?php

class FormatConverter
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

:+1:

```php
<?php

class StaticFormatConverter
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

<br>

### Remove extra around public/protected/private/static modifiers and const

- class: [`Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer`](packages/coding-standard/src/Fixer/Spacing/RemoveSpacingAroundModifierAndConstFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer: null
```

```diff
 class SomeClass
 {
-    protected     static     $value;
+    protected static $value;
}
```

<br>

### Bool Property should have default value, to prevent unintentional null comparison

- class: [`Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer`](/packages/coding-standard/src/Fixer/Property/BoolPropertyDefaultValueFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer: null
```

```diff
 <?php

 class SomeClass
 {
     /**
      * @var bool
      */
-    private $booleanProperty;
+    private $booleanProperty = true;

     public function run()
     {
         if (! $this->booleanProperty) {
             // ...
         }
     }
}
```

<br>

### Make sure That `@param`, `@var`, `@return` and `@throw` Types Exist

- class: [`Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff`](src/Sniffs/Commenting/AnnotationTypeExistsSniff.php)

```yaml
services:
    Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff: null
```

:x:

```php
<?php

class SomeClass
{
    /**
     * @var NonExistingClass
     */
    private $property;
}
```

:+1:

```php
<?php

class SomeClass
{
    /**
     * @var ExistingClass
     */
    private $property;
}
```

<br>

### Use Unique Class Short Names

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff`](/src/Sniffs/Architecture/DuplicatedClassShortNameSniff.php)

:x:

```php
<?php

namespace App;

class Finder
{
}
```

```php
<?php

namespace App\Entity;

class Finder
{
}
```

:+1:

```diff
 <?php

 namespace App\Entity;

-class Finder
+class EntityFinder
 {
 }
```

Do you want skip some classes? Configure it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff:
        allowed_class_names:
            - 'Request'
            - 'Response'
```

<br>

### Make `@param`, `@return` and `@var` Format United

- class: [`Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`](src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

```yaml
services:
    Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer: null
```

```diff
 <?php

 /**
- * @param $name string
+ * @param string $name
- * @return int $value
+ * @return int
  */
 function someFunction($name)
 {
 }

 class SomeClass
 {
     /**
-     * @var int $property
+     * @var int
      */
     private $property;
 }

-/* @var int $value */
+/** @var int $value */
 $value = 5;

-/** @var $value int */
+/** @var int $value */
 $value = 5;
```

<br>

### Order Private Methods by Their Use Order

- class: [`Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer`](src/Fixer/Order/PrivateMethodOrderByUseFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer: null
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call2()
    {
    }

    private function call1()
    {
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call1()
    {
    }

    private function call2()
    {
    }
}
```

<br>

### Order Properties From Simple to Complex

Properties are ordered by visibility first, then by complexity.

- class: [`Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer`](src/Fixer/Order/PropertyOrderByComplexityFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer: null
```

:x:

```php
<?php

final class SomeFixer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $service;

    /**
     * @var int
     */
    private $price;
}
```

:+1:

```php
<?php

final class SomeFixer
{
    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $service;
}
```

<br>

### Prefer Another Class

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff`](src/Sniffs/Architecture/PreferredClassSniff.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff:
        oldToPreferredClasses:
            DateTime: 'Nette\Utils\DateTime'
```

:x:

```php
<?php

$dateTime = new DateTime('now');
```

:+1:

```php
<?php

$dateTime = new Nette\Utils\DateTime('now');
```

<br>

### Indexed PHP arrays should have 1 item per line

- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`](src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

### There should not be empty PHPDoc blocks

Just like `PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer`, but this one removes all doc block lines.

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer`](src/Fixer/Commenting/RemoveEmptyDocBlockFixer.php)

```diff
-/**
- */
 public function someMethod()
 {
 }
```

<br>

### Block comment should not have 2 empty lines in a row

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer`](src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

```diff
 /**
  * @param int $value
  *
- *
  * @return array
  */
 public function setCount($value)
 {
 }
```

<br>

### Parameters, arguments and array items should be on the same/standalone line to fit line length

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`](src/Fixer/LineLength/LineLengthFixer.php)

```diff
 class SomeClass
 {
-    public function someMethod(SuperLongArguments $superLongArguments, AnotherLongArguments $anotherLongArguments, $oneMore)
+    public function someMethod(
+        SuperLongArguments $superLongArguments,
+        AnotherLongArguments $anotherLongArguments,
+        $oneMore
+    )
     {
     }

-    public function someOtherMethod(
-        ShortArgument $shortArgument,
-        $oneMore
-    ) {
+    public function someOtherMethod(ShortArgument $shortArgument, $oneMore) {
     }
 }
```

- Are 120 characters too long for you?
- Do you want to break longs lines but not inline short lines or vice versa?

**Change it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer:
        max_line_length: 100 # default: 120
        break_long_lines: true # default: true
        inline_short_lines: false # default: true
```

<br>

### Property name should match its key, if possible

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer`](src/Fixer/Naming/PropertyNameMatchingTypeFixer.php)

```diff
-public function __construct(EntityManagerInterface $eventManager)
+public function __construct(EntityManagerInterface $entityManager)
 {
-    $this->eventManager = $eventManager;
+    $this->entityManager = $entityManager;
 }
```

This checker ignores few **system classes like `std*` or `Spl*` by default**. In case want to skip more classes, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer:
        extra_skipped_classes:
            - 'MyApp*' # accepts anything like fnmatch
```

<br>

### Public Methods Should have Specific Order by Interface/Parent Class

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer`](src/Fixer/Order/MethodOrderByTypeFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer:
        method_order_by_type:
            Rector\Contract\Rector\PhpRectorInterface:
                - 'getNodeTypes'
                - 'refactor'
```

↓

```diff
 final class SomeRector implements PhpRectorInterface
 {
-    public function refactor()
+    public function getNodeTypes()
     {
-        // refactoring
+        return ['SomeType'];
     }
-
-    public function getNodeTypes()
+    public function refactor(): void
     {
-        return ['SomeType'];
+        // refactoring
     }
 }
```

<br>

### `::class` references should be used over string for classes and interfaces

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer`](src/Fixer/Php/ClassStringToClassConstantFixer.php)

```diff
-$className = 'DateTime';
+$className = DateTime::class;
```

This checker takes **only existing classes by default**. In case want to check another code not loaded by local composer, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        class_must_exist: false # true by default
```

Do you want to allow some classes to be in string format?

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        allow_classes:
            - 'SomeClass'
```

<br>

### Array property should have default value, to prevent undefined array issues

- class: [`Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer`](src/Fixer/Property/ArrayPropertyDefaultValueFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer: null
```

```diff
 class SomeClass
 {
     /**
      * @var string[]
      */
-    public $apples;
+    public $apples = [];

     public function run()
     {
         foreach ($this->apples as $mac) {
             // ...
         }
     }
 }
```

<br>

### Strict types declaration has to be followed by empty line

- class: [`Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`](src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```diff
 <?php

declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

### Use explicit and informative exception names over generic ones

- class: [`Symplify\CodingStandard\Sniffs\Architecture\ExplicitExceptionSniff`](src/Sniffs/Architecture/ExplicitExceptionSniff.php)

:x:

```php
<?php

throw new RuntimeException('...');
```

:+1:

```php
<?php

throw new FileNotFoundException('...');
```

<br>

### Class "X" cannot be parent class. Use composition over inheritance instead.

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff`](src/Sniffs/CleanCode/ForbiddenParentClassSniff.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff:
        forbiddenParentClasses:
            - 'Doctrine\ORM\EntityRepository'
            # again, you can use fnmatch() pattern
            - '*\AbstractController'
```

:x:

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```

:+1:

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }
}
```

<br>

### Use explicit return values over magic "&$variable" reference

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff`](src/Sniffs/CleanCode/ForbiddenReferenceSniff.php)

:x:

```php
<?php

function someFunction(&$var)
{
    $var + 1;
}
```

:+1:

```php
<?php

function someFunction($var)
{
    return $var + 1;
}
```

<br>

### Use services and constructor injection over static method

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff`](src/Sniffs/CleanCode/ForbiddenStaticFunctionSniff.php)

:x:

```php
<?php

class SomeClass
{
    public static function someFunction()
    {
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function someFunction()
    {
    }
}
```

<br>

### Constant should have docblock comment

- class: [`Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff`](src/Sniffs/Commenting/VarConstantCommentSniff.php)

```php
class SomeClass
{
    private const EMPATH_LEVEL = 55;
}
```

:+1:

```php
<?php

class SomeClass
{
    /**
     * @var int
     */
    private const EMPATH_LEVEL = 55;
}
```

<br>

### Use per line assign instead of multiple ones

- class: [`Symplify\CodingStandard\Sniffs\ControlStructure\ForbiddenDoubleAssignSniff`](src/Sniffs/ControlStructure/ForbiddenDoubleAssignSniff.php)

:x:

```php
<?php

$value = $anotherValue = [];
```

:+1:

```php
<?php

$value = [];
$anotherValue = [];
```

<br>

### There should not be comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](src/Sniffs/Debug/CommentedOutCodeSniff.php)

:x:

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

<br>

### Debug functions should not be left in the code

- class: [`Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff`](src/Sniffs/Debug/DebugFunctionCallSniff.php)

:x:

```php
<?php

d($value);
dd($value);
dump($value);
var_dump($value);
```

<br>

### Class should have suffix by parent class/interface

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff`](src/Sniffs/Naming/ClassNameSuffixByParentSniff.php)

:x:

```php
<?php

class Some extends Command
{
}
```

:+1:

```php
<?php

class SomeCommand extends Command
{
}
```

This checker check few names by default. But if you need, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff:
        parentTypesToSuffixes:
            # defaults
            - 'Command'
            - 'Controller'
            - 'Repository'
            - 'Presenter'
            - 'Request'
            - 'Response'
            - 'EventSubscriber'
            - 'FixerInterface'
            - 'Sniff'
            - 'Exception'
            - 'Handler'
```

Or keep all defaults values by using `extra_parent_types_to_suffixes`:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff:
        extraParentTypesToSuffixes:
            - 'ProviderInterface'
```

It also covers `Interface` suffix as well, e.g `EventSubscriber` checks for `EventSubscriberInterface` as well.

### Object Calisthenics rules

- From [Object Calisthenics](https://tomasvotruba.com/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/)
- [Original source for PHPStan rules](https://github.com/object-calisthenics/phpcs-calisthenics-rules/)

### No `else` And `elseif`

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoElseAndElseIfRule.php)

```neon
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule
```

:x:

```php
<?php

if ($value) {
    return 5;
} else {
    return 10;
}
```

:+1:

```php
if ($value) {
    return 5;
}

return 10;
```

<br>

### No Names Shorter than 3 Chars

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoShortNameRule.php)

```neon
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule
```

:x:

```php
<?php

class EM
{
}
```

:+1:

```php
<?php

class EntityManager
{
}
```

<br>

### No setter methods

- class: [`\Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoSetterClassMethodRule.php)

```neon
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule
```

:x:

```php
<?php

final class Person
{
    private string $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
```

:+1:

```php
final class Person
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

<br>

### No Chain Method Call

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoChainMethodCallRule.php)
- Check [Fluent Interfaces are Evil](https://ocramius.github.io/blog/fluent-interfaces-are-evil/)

```neon
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        return $this->create()->modify()->save();
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function run()
    {
        $object = $this->create();
        $object->modify();
        $object->save();

        return $object;
    }
}
```

<br>

## Contributing

Open an [issue](https://github.com/symplify/symplify/issues) or send a [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
