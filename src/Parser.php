<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\Dispatcher;
use Panlatent\Annotation\Parser\Exception as ParserException;
use Panlatent\Annotation\Parser\GrammarAnalyzer;
use Panlatent\Annotation\Parser\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Preprocessor;

class Parser
{
    /**
     * @var \Panlatent\Annotation\TagVendor
     */
    protected $tagVendor;

    /**
     * Parser constructor.
     *
     * @param \Panlatent\Annotation\TagVendor|null $vendor
     */
    public function __construct(TagVendor $vendor = null)
    {
        if ( ! $vendor) {
            $this->tagVendor = new TagVendor();
        } else {
            $this->tagVendor = $vendor;
        }
    }

    /**
     * @param string $docComment
     * @return \Panlatent\Annotation\PhpDoc
     * @throws \Panlatent\Annotation\Parser\Exception
     */
    public function parser($docComment)
    {
        $preprocessor = new Preprocessor();
        if ( ! $preprocessor->check($docComment)) {
            throw new ParserException('DocComment format error');
        }
        $phpdoc = $preprocessor->preprocessor($docComment);

        $lexer = new LexicalAnalyzer();
        $dispatcher = new Dispatcher($lexer->tokenization($phpdoc));
        $dispatcher->transfer([new GrammarAnalyzer($this->tagVendor), 'phpdocization']);

        return $dispatcher->handle();
    }

    /**
     * @return \Panlatent\Annotation\TagVendor
     */
    public function getTagVendor()
    {
        return $this->tagVendor;
    }

    /**
     * @param \Panlatent\Annotation\TagVendor $tagVendor
     */
    public function setTagVendor($tagVendor)
    {
        $this->tagVendor = $tagVendor;
    }
}