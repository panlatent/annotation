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

class Tag extends TagAbstract
{
    protected $name;

    protected $description;

    protected $withSignature = false;

    public function __construct($name, $description = '')
    {
        parent::__construct($name);
    }

    public static function create($name, $content)
    {
        return new static($name, $content);
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