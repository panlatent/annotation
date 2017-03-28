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

use Panlatent\Annotation\Tag\ApiTag;
use Panlatent\Annotation\Tag\AuthorTag;
use Panlatent\Annotation\Tag\CategoryTag;
use Panlatent\Annotation\Tag\CopyrightTag;
use Panlatent\Annotation\Tag\DeprecatedTag;
use Panlatent\Annotation\Tag\ExampleTag;
use Panlatent\Annotation\Tag\GlobalTag;
use Panlatent\Annotation\Tag\InternalTag;
use Panlatent\Annotation\Tag\LicenseTag;
use Panlatent\Annotation\Tag\LineTag;
use Panlatent\Annotation\Tag\MethodTag;
use Panlatent\Annotation\Tag\PackageTag;
use Panlatent\Annotation\Tag\ParamTag;
use Panlatent\Annotation\Tag\PropertyTag;
use Panlatent\Annotation\Tag\ReturnTag;
use Panlatent\Annotation\Tag\SeeTag;
use Panlatent\Annotation\Tag\SinceTag;
use Panlatent\Annotation\Tag\SubpackageTag;
use Panlatent\Annotation\Tag\ThrowsTag;
use Panlatent\Annotation\Tag\TodoTag;
use Panlatent\Annotation\Tag\UsesTag;
use Panlatent\Annotation\Tag\VarTag;
use Panlatent\Annotation\Tag\VersionTag;

class TagVendor
{
    /**
     * @var bool
     */
    protected $autoload = true;

    /**
     * @var bool
     */
    protected $deprecated = false;

    /**
     * @var string
     */
    protected $defaultTag;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * TagVendor constructor.
     *
     * @param bool $withStandard
     * @param null $defaultTag
     */
    public function __construct($withStandard = true, $defaultTag = null)
    {
        if ( ! $defaultTag) {
            $this->defaultTag = Tag::class;
        }
        if ($withStandard) {
            foreach (static::getStandardTags() as $name => $class) {
                $this->register($class, $name);
            }
        }
    }

    /**
     * @return array
     */
    public static function getStandardTags()
    {
        return [
            'api'        => ApiTag::class,
            'author'     => AuthorTag::class,
            'copyright'  => CopyrightTag::class,
            'deprecated' => DeprecatedTag::class,
            'example'    => ExampleTag::class,
            'global'     => GlobalTag::class,
            'internal'   => InternalTag::class,
            'license'    => LicenseTag::class,
            'method'     => MethodTag::class,
            'package'    => PackageTag::class,
            'param'      => ParamTag::class,
            'property'   => PropertyTag::class,
            'return'     => ReturnTag::class,
            'see'        => SeeTag::class,
            'since'      => SinceTag::class,
            'throws'     => ThrowsTag::class,
            'todo'       => TodoTag::class,
            'uses'       => UsesTag::class,
            'var'        => VarTag::class,
            'version'    => VersionTag::class,
        ];
    }

    /**
     * @return array
     */
    public static function getDeprecatedTags()
    {
        return [
            'category'   => CategoryTag::class,
            'link'       => LineTag::class,
            'subpackage' => SubpackageTag::class,
        ];
    }

    /**
     * @param \Panlatent\Annotation\Tag $tag
     */
    public function add(Tag $tag)
    {
        $name = $tag->getName();

        if ($tag instanceof TagSpecializationInterface) {
            $specialization = $tag->getSpecialization();
        } else {
            $specialization = '';
        }

        $this->register(get_class($tag), $name, $specialization);
    }

    /**
     * @param string $name
     * @param string $specialization
     * @return mixed
     * @throws \Panlatent\Annotation\NotFoundException
     */
    public function get($name, $specialization = '')
    {
        if ( ! $this->has($name, $specialization)) {
            if (in_array($name, array_keys(static::getDeprecatedTags()))) {
                throw new NotFoundException("The @$name tag is deprecated [PSR-5 DRAFT]");
            }

            $specialization = $specialization ? ':' . $specialization : '';
            throw new NotFoundException('Not found tag name: @' . $name . $specialization);
        }

        return $this->tags[$name][$specialization];
    }

    /**
     * @param string $name
     * @param string $specialization
     * @return bool
     */
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

    /**
     * @param string $name
     * @param string $specialization
     * @throws \Panlatent\Annotation\NotFoundException
     */
    public function remove($name, $specialization = '')
    {
        if ( ! isset($this->tags[$name][$specialization])) {
            throw new NotFoundException("Not exists tag $name:$specialization in tag vendor");
        }

        unset($this->tags[$name][$specialization]);
        if (empty($this->tags[$name])) {
            unset($this->tags[$name]);
        }
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $specialization
     */
    public function register($class, $name, $specialization = '')
    {
        if ( ! isset($this->tags[$name])) {
            $this->tags[$name] = [];
        }
        $this->tags[$name][$specialization] = $class;
    }

    /**
     * @return bool
     */
    public function isAutoload()
    {
        return $this->autoload;
    }

    public function withAutoload()
    {
        $this->autoload = true;
    }

    public function withoutAutoload()
    {
        $this->autoload = false;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getDefaultTag()
    {
        return $this->defaultTag;
    }

    /**
     * @param $class
     */
    public function setDefaultTag($class)
    {
        $this->defaultTag = $class;
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->deprecated;
    }

    public function withDeprecated()
    {
        $this->deprecated = true;
        foreach (static::getDeprecatedTags() as $name => $class) {
            $this->register($class, $name);
        }
    }

    public function withoutDeprecated()
    {
        $this->deprecated = false;
        foreach (static::getDeprecatedTags() as $name => $class) {
            $this->remove($name);
        }
    }

    /**
     * @param string $tagName
     * @param string $specialization
     * @return string
     */
    protected function getClassName($tagName, $specialization)
    {
        $tagName = str_replace('-', '', ucwords(str_replace('_', '-', $tagName), '-'));
        if ($specialization) {
            $specialization = '\\' . str_replace('-', '', ucwords($tagName, '-')) . TagSpecializationInterface::CLASS_NAME_SUFFIX;
        }

        return $tagName . Tag::CLASS_NAME_SUFFIX . $specialization;
    }
}