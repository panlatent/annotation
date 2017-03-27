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

它的目的是帮助用户轻松的获取 PHPDoc 注释中包含的信息。你可以使用它构建你的项目。使用继承关系特化标签或是创建自定义标签，来拓展解析器。

## Usage

## Parser

Annotation 的解析器由 4 个部分组成，包括预处理器、字符流生成器、词法分析器、语法分析器。

### 预处理器

### 字符流生成器

是用来生成字符流的类，专门针对字符流提供迭代器、预期和回溯操作、并提供字符的行号、列号和上下文信息。

### 词法分析器

词法分析器会将字符流生成器送来的字符按照一定规则解析成 Token 。生成的 Token 以 yield (Generator PHP5.5+) 的方式输出，
并在必要的时候抛出异常。

### 语法分析器

语法分析器从词法分析器中接受 Token 流，分析并生成语法树并组装成PHP类结构。当词法分析器抛出异常时，由语法分析器决定是否终止
 PHPDoc 的解析，因为词法分析器是可以重入的。
 
### 自定义标签

创建一个类并继承自 `Panlatent\Annotation\Parser\Tag` ，类名后缀为 `Tag`。

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

创建一个类并继承自 `Panlatent\Annotation\Parser\Tag`，并实现 `Panlatent\Annotation\Parser\TagSpecializationInterface` ，
类名后缀为 `Specialization` 。推荐做法是继承已有的标签类，将特化标签类放入与该标签类同名的目录下。

例如，特化 `@api` 标签 `@api:restful`，创建一个类名为 `RestfulSpecialization`，并继承 
`Panlatent\Annotation\Parser\Tag\ApiTag` 。

> 需要注意的是，特化标签也是依照命名空间和类名查找类文件。如果标签名与命名空间路径不匹配，需要手动注册标签。这意味所有内置标签都要通过
手动注册来特化。

```php
/**
 * @api:restful post
 */
```

## License

The Annotation is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).