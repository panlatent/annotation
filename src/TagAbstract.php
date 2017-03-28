<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

abstract class TagAbstract implements TagInterface
{
    const DEFINED_TAG_NAMESPACE = 'Panlatent\\Annotation\\Tag\\';
    const CLASS_NAME_SUFFIX = 'Tag';

    protected $name;

    public function __construct($name = null)
    {
        if (empty($this->name)) {
            if (empty($name)) {
                $name = static::class;
                if (self::CLASS_NAME_SUFFIX == substr($name, -strlen(self::CLASS_NAME_SUFFIX))) {
                    $name = substr($name, 0, -strlen(self::CLASS_NAME_SUFFIX));
                }
                if (0 === strncmp($name, self::DEFINED_TAG_NAMESPACE, strlen(self::DEFINED_TAG_NAMESPACE))) {
                    $name = strtolower(substr($name, strlen(self::DEFINED_TAG_NAMESPACE)));
                }
            }
            $this->name = $name;
        }
    }

    public function getName()
    {
        return $this->name;
    }
}