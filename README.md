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

它的目的并不是根据注释生成文档，而是帮助用户轻松的获取 PHPDoc 注释中包含的信息。

## License

The Annotation is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).