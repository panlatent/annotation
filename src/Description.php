<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

class Description
{
    const TEXT = 0;
    const INLINE = 1;

    protected $text;

    protected $phpdoc;

    public function __construct($text = '', PhpDoc $phpdoc = null)
    {
        $this->text = $text;
        $this->phpdoc = $phpdoc;
    }

    public static function create($parser, TagFactory $factory)
    {
        if (empty($parser)) {
            return null;
        }

        $description = new static();
        if (is_string($parser)) {
            $description->text = $parser;
        } else {
            $description->phpdoc = PhpDoc::create($parser, $factory);
        }

        return $description;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return \Panlatent\Annotation\PhpDoc
     */
    public function getPhpdoc()
    {
        return $this->phpdoc;
    }
}