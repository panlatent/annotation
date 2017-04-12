# Annotation
[![Build Status](https://travis-ci.org/panlatent/annotation.svg)](https://travis-ci.org/panlatent/annotation)
[![Latest Stable Version](https://poser.pugx.org/panlatent/annotation/v/stable.svg)](https://packagist.org/packages/panlatent/annotation) 
[![Total Downloads](https://poser.pugx.org/panlatent/annotation/downloads.svg)](https://packagist.org/packages/panlatent/annotation) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/annotation/v/unstable.svg)](https://packagist.org/packages/panlatent/annotation) 
[![License](https://poser.pugx.org/panlatent/annotation/license.svg)](https://packagist.org/packages/panlatent/annotation)

Parsing PHPDoc style annotations from comments.

Annotation 是一个 PHPDoc 风格注释解析器，它能从注释里面解析 PHPDoc 注释元素并封装成对应的类。为了保证对 PHPDoc 注释解析的一致性，
Annotation 遵循 [PSR-5 PHPDoc草案](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)。
与其他大多数库不同，它的解析器通过词法分析来解析 PHPDoc 。

它的目的是帮助用户轻松的获取 PHPDoc 注释中包含的信息。你可以使用它构建你的项目。使用继承关系特化标签或是创建自定义标签。

## Usage

### 仅解析 PHPDoc

如果只需要解析 PHPDoc，仅需要构造解析器并调用 `Parser::parser()` 方法：
```php
$docBlock = <<<DOC
/**
 * This is a phpdoc.
 */
DOC;

$parser = new Panlatent\Annotation\Parser();
$phpdoc = $parser->parser($docBlock);

echo $phpdoc->getSummary(); // Output: "This is a phpdoc."
```

### 解析 PHP 代码（使用反射）

```php
$parser = new Panlatent\Annotation\Parser();
$annotation = new AnnotationClass(ExampleClass::class, $parser);
```

### 自定义标签

#### 注册

自定义标签需要注册到 `TagFactory` 中，如果解析时发现未注册标签，将按照类似 `PSR-4` 的规则进行类查找。

```php
$factory = new Panlatent\Annotation\TagFactory();
$factory->add('api', Panlatent\Annotation\Tag\ApiTag::class);
$parser = new Parser($factory);
```
或者
```php
$parser = new Parser();
$factory = $parser->getTagFactory();
$factory->add('api', Panlatent\Annotation\Tag\ApiTag::class);
```

#### 创建

创建一个类, 继承自 `Panlatent\Annotation\Parser\Tag` 或实现 `Panlatent\Annotation\Parser\TagInterface`，类名后缀为 `Tag`。

例如，创建一个 `\panlatent\annotation\add` 标签, 类名为 `Panlatent\Annotation\AddTag`。
```php
/**
 * @\panlatent\annotation\add // 最前面的 '/' 不是必须的
 */
```

也可以创建一个别名，这需要你提前将别名注册，并绑定到一个类上：
```php
/**
 * @add
 */
```

### 特化标签

特化标签形如 `@api:restful` ，推荐使用类的继承关系表示特化。特化标签与普通标签类明明规则相同。
例如，特化 `@api` 标签 `@api:restful`，对应类名为 `ApiRestfulTag`。

```php
/**
 * @api:restful post
 */
```

## Parser

Annotation 的解析器由 4 个部分组成，包括预处理器、字符流生成器、词法分析器、语法分析器。

+ **预处理器**是用来过滤包裹 PHPDoc `*` 符号或者无意义空白的工具。它可以将 `*` 替换为等效的空格，目的是保留原始字符串的未知
信息，也可以仅保留有效的PHPDoc。

+ **字符流生成器**是用来生成字符流的类，专门针对字符流提供迭代器、预期和回溯操作、并提供字符的行号、列号和上下文信息。

+ **词法分析器**会将字符流生成器送来的字符按照一定规则解析成 Token 。生成的 Token 以 yield (Generator PHP5.5+) 的方式输出，
并在必要的时候抛出异常。

+ **语法分析器**从词法分析器中接受 Token 流，分析并生成语法树并组装成PHP类结构。

## License

The Annotation is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).