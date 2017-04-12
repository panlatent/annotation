<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\Exception as ParserException;
use Panlatent\Annotation\Parser\Lexical\CharacterScanner;
use Panlatent\Annotation\Parser\Lexical\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Preprocessor;
use Panlatent\Annotation\Parser\Syntax\SyntaxAnalyzer;

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
        $docBlock = $preprocessor->preprocessor($docComment);

        $scanner = new CharacterScanner($docBlock);
        $lexer = new LexicalAnalyzer($scanner);
        $syntax = new SyntaxAnalyzer($lexer);

        return PhpDoc::create($syntax->phpdocization(), $this->tagVendor);
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