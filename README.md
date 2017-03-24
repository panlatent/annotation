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

它的目的是帮助用户轻松的获取 PHPDoc 注释中包含的信息。你可以使用它构建你的项目。

## Usage

## Parser

Annotation 的解析器由 4 个部分组成，包括预处理器、字符流生成器、词法分析器、语法分析器。

### 预处理器

### 字符流生成器

生成字符流、提供字符信息（行号，列号）、提供预期／回溯操作、提供字符上下文

## License

The Annotation is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).