<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use ReflectionClass;

class AnnotationClass extends Annotation
{
    protected $phpdoc;

    protected $reflection;

    public function __construct($class, $parser = null)
    {
        parent::__construct($parser);

        $this->reflection = new ReflectionClass($class);
        $docComment = $this->reflection->getDocComment();
        $this->phpdoc = $this->parser->parser($docComment);
    }
}