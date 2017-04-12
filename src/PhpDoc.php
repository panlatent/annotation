<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

class PhpDoc
{
    /**
     * @var string
     */
    protected $summary;

    /**
     * @var \Panlatent\Annotation\Description
     */
    protected $description;

    /**
     * @var \Panlatent\Annotation\TagStorage
     */
    protected $tags;

    /**
     * PhpDoc constructor.
     *
     * @param string                                   $summary
     * @param \Panlatent\Annotation\Description|null   $description
     * @param \Panlatent\Annotation\TagStorage|null    $tags
     */
    public function __construct($summary = '', Description $description = null, TagStorage $tags = null)
    {
        $this->summary = $summary;
        $this->description = $description;
        if ( ! $tags) {
            $this->tags = new TagStorage();
        } else {
            $this->tags = $tags;
        }
    }

    public static function create(array $parser, TagVendor $vendor)
    {
        return new static(
            $parser['summary'],
            Description::create($parser['description'], $vendor),
            TagStorage::create($parser['tags'], $vendor)
        );
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return \Panlatent\Annotation\Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getTag($name)
    {
        return $this->tags->get($name);
    }

    /**
     * @return \Panlatent\Annotation\TagStorage
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function hasTag($name)
    {
        $this->tags->has($name);
    }
}