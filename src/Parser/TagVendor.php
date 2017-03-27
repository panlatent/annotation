<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class TagVendor
{
    protected $autoload = true;

    protected $default;

    protected $tags = [];

    public function __construct($default = null)
    {
        if ( ! $default) {
            $this->default = Tag::class;
        }
    }

    public function get($name, $specialization = '')
    {
        if ( ! $this->has($name, $specialization)) {
            $specialization = $specialization ? ':' . $specialization : '';
            throw new NotFoundException('Not found ' . $name . $specialization);
        }

        return $this->tags[$name][$specialization];
    }

    public function has($name, $specialization = '')
    {
        if (isset($this->tags[$name][$specialization])) {
            return true;
        } elseif ( ! $this->autoload) {
            return false;
        }

        $className = $this->getClassName($name, $specialization);
        if ( ! class_exists($className, true) || ! in_array(Tag::class, get_parent_class($className))) {
            return false;
        }
        $this->register($className, $name, $specialization);

        return true;
    }

    public function register($class, $name, $specialization = '')
    {
        if ( ! isset($this->tags[$name])) {
            $this->tags[$name] = [];
        }
        $this->tags[$name][$specialization] = $class;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($class)
    {
        $this->default = $class;
    }

    protected function getClassName($tagName, $specialization)
    {
        $tagName = str_replace('-', '', ucwords(str_replace('_', '-', $tagName), '-'));
        if ($specialization) {
            $specialization = '\\' . str_replace('-', '', ucwords($tagName, '-')) . TagSpecializationInterface::CLASS_NAME_SUFFIX;
        }

        return $tagName . TagInterface::CLASS_NAME_SUFFIX . $specialization;
    }
}