<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\Tag;

class PhpDocFactory
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
     * @var string
     */
    protected $product;

    public function __construct()
    {
        $this->tags = new TagStorage();
    }

    /**
     * @return \Panlatent\Annotation\PhpDoc
     */
    public function create()
    {
        if ( ! $this->product) {
            $this->product = Annotation::class;
        }

        return new ($this->product)($this->summary, $this->description, $this->tags);
    }

    public function addTag(Tag $tag)
    {
        $this->tags->attach($tag);
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setProduct($product)
    {
        if ( ! class_exists($product)) {
            throw new Exception('Product class not exists');
        } elseif ( ! in_array(PhpDoc::class ,get_parent_class($product))) {
            throw new Exception('Product class must extends ' . PhpDoc::class);
        }

        $this->product = $product;
    }
}