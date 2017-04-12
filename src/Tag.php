<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

class Tag implements TagInterface
{
    const DEFINED_TAG_NAMESPACE = 'Panlatent\\Annotation\\Tag\\';
    const CLASS_NAME_SUFFIX = 'Tag';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Panlatent\Annotation\Description
     */
    protected $description;

    /**
     * @var bool
     */
    protected $withSignature = false;

    public static function create(Description $description)
    {
        $class = get_called_class();
        throw new Exception("Free tag must have a name or subclass {$class} override the static method");
    }

    public static function createFromName($name, Description $description)
    {
        $tag = new static();
        $tag->name = $name;
        $tag->description = $description;

        return $tag;
    }

    public function isWithSignature()
    {
        return $this->withSignature;
    }

    public function getName()
    {
        if ( ! empty($this->name)) {
            return $this->name;
        }

        $name = static::class;
        if (self::CLASS_NAME_SUFFIX == substr($name, - strlen(self::CLASS_NAME_SUFFIX))) {
            $name = substr($name, 0, - strlen(self::CLASS_NAME_SUFFIX));
        }
        if (0 === strncmp($name, self::DEFINED_TAG_NAMESPACE, strlen(self::DEFINED_TAG_NAMESPACE))) {
            $name = strtolower(substr($name, strlen(self::DEFINED_TAG_NAMESPACE)));
        }

        return $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }
}