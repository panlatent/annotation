<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

abstract class Tag implements TagInterface
{
    protected $name;

    protected $specialization;

    protected $details;

    public function __construct($specialization = null, $details = null)
    {
        $this->specialization = $specialization;
        $this->details = $details;
    }

    public function getName()
    {
        return $this->name;
    }
}