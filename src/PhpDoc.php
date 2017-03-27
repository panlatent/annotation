<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

abstract class PhpDoc
{
    /**
     * @var string
     */
    protected $summary;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \Panlatent\Annotation\TagStorage
     */
    protected $tags;

    /**
     * PhpDoc constructor.
     *
     * @param string                                $summary
     * @param string                                $description
     * @param \Panlatent\Annotation\TagStorage|null $tags
     */
    public function __construct($summary = '', $description = '', TagStorage $tags = null)
    {
        $this->summary = $summary;
        $this->description = $description;
        if ( ! $tags) {
           $tags = new TagStorage();
        }
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \Panlatent\Annotation\TagStorage
     */
    public function getTags()
    {
        return $this->tags;
    }
}