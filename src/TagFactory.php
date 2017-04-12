<?php
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

class TagFactory
{
    /**
     * @var bool
     */
    protected $withStandardTags;

    /**
     * @var bool
     */
    protected $withDeprecatedTags;

    /**
     * @var bool
     */
    protected $withPsr4Register;

    /**
     * @var array
     */
    protected $availableTags = [];

    /**
     * @var string
     */
    protected $slackTag = SlackTag::class;

    /**
     * @var array
     */
    protected static $standardTags = [
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

    /**
     * @var array
     */
    protected static $deprecatedTags = [
        'category'   => CategoryTag::class,
        'link'       => LineTag::class,
        'subpackage' => SubpackageTag::class,
    ];

    public function __construct($psr4Register = true)
    {
        $this->withPsr4Register = $psr4Register;
        $this->withStandardTags();
        $this->withDeprecatedTags();
    }

    /**
     * @return array
     */
    public static function getStandardTags()
    {
        return self::$standardTags;
    }

    /**
     * @return array
     */
    public static function getDeprecatedTags()
    {
        return self::$deprecatedTags;
    }

    public function create($name, $description)
    {
        if ( ! $this->has($name) || ! $this->withPsr4Register || ! $this->psr4Register($name)) {
            if (empty($this->slackTag)) {
                throw new NotFoundException("Not found tag name: @{$name}");
            }
            $class = $this->slackTag;
        } else {
            $class = $this->get($name);
        }
        if ( ! is_subclass_of($class, TagAbstractFactory::class)) {
            return new $class();
        } elseif (is_subclass_of($class, TagWithNameFactory::class)) {
            return $class::createWithName($name, $description);
        } elseif (is_subclass_of($class, TagWithoutNameFactory::class)) {
            return $class::create($description);
        }

        throw new Exception("Undefined create tag factory method");
    }

    /**
     * @param string $class
     * @param string $name
     */
    public function register($name, $class)
    {
        $this->availableTags[$name] = $class;
    }

    public function has($name)
    {
        return isset($this->availableTags[$name]) || $this->psr4Register($name);
    }

    public function get($name)
    {
        if ( ! $this->has($name)) {
            if (in_array($name, array_keys(static::$deprecatedTags))) {
                throw new NotFoundException("The @{$name} tag is deprecated in PSR-5 DRAFT");
            }
            throw new NotFoundException("Not found tag name: @{$name}");
        }

        return $this->availableTags[$name];
    }

    public function remove($name)
    {
        unset($this->availableTags[$name]);
    }

    /**
     * @return string
     */
    public function getSlackTag()
    {
        return $this->slackTag;
    }

    /**
     * @param string $slackTag
     * @throws \Panlatent\Annotation\Exception
     */
    public function setSlackTag($slackTag)
    {
        if ( ! is_subclass_of($slackTag, TagWithNameFactory::class)) {
            throw new Exception("Slack tag must implements TagWithNameFactory Interface");
        }

        $this->slackTag = $slackTag;
    }

    /**
     * @return array
     */
    public function getAvailableTags()
    {
        return $this->availableTags;
    }

    /**
     * @return bool
     */
    public function isWithStandardTags()
    {
        return $this->withStandardTags;
    }

    public function withStandardTags()
    {
        $this->withStandardTags = true;
        $this->availableTags = array_merge($this->availableTags, static::$standardTags);
    }

    public function withoutStandardTags()
    {
        $this->withStandardTags = false;
        $this->availableTags = array_diff_key($this->availableTags, static::$standardTags);
    }

    /**
     * @return bool
     */
    public function isWithDeprecatedTags()
    {
        return $this->withDeprecatedTags;
    }

    public function withDeprecatedTags()
    {
        $this->withDeprecatedTags = true;
        $this->availableTags = array_merge($this->availableTags, static::$deprecatedTags);
    }

    public function withoutDeprecatedTags()
    {
        $this->withDeprecatedTags = false;
        $this->availableTags = array_diff_key($this->availableTags, static::$deprecatedTags);
    }

    /**
     * @return bool
     */
    public function isWithPsr4Register()
    {
        return $this->withPsr4Register;
    }

    public function withPsr4Register()
    {
        $this->withPsr4Register = true;
    }

    public function withoutPsr4Register()
    {
        $this->withPsr4Register = false;
    }

    protected function psr4Register($name)
    {
        $class = $this->covertClassName($name);
        if ( ! class_exists($class, true)) {
            return false;
        }
        $this->register($name, $class);

        return true;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function covertClassName($name)
    {
        if (false !== ($pos = strpos($name, ':'))) {
            $name = substr($name, 0, $pos) . '-' . substr($name, $pos + 1);
        }

        return str_replace('-', '', ucwords(str_replace('_', '-', $name), '-')) . 'Tag';
    }

}