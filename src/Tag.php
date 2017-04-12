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
    const DEFINED_TAG_NAMESPACE = 'Panlatent\\Annotation\\Tag\\';
    const CLASS_NAME_SUFFIX = 'Tag';

    /**
     * @var string
     */
    protected $name;

    public function getName()
    {
        if ( ! empty($this->name)) {
            return $this->name;
        }

        return $this->name = $this->getNameFromClass();
    }

    protected function getNameFromClass()
    {
        $name = static::class;
        if (self::CLASS_NAME_SUFFIX == substr($name, - strlen(self::CLASS_NAME_SUFFIX))) {
            $name = substr($name, 0, - strlen(self::CLASS_NAME_SUFFIX));
        }
        if (0 === strncmp($name, self::DEFINED_TAG_NAMESPACE, strlen(self::DEFINED_TAG_NAMESPACE))) {
            $name = strtolower(substr($name, strlen(self::DEFINED_TAG_NAMESPACE)));
        }

        return $name;
    }
}