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
     * @var \Panlatent\Annotation\TagFactory
     */
    protected $tagFactory;

    /**
     * Parser constructor.
     *
     * @param \Panlatent\Annotation\TagFactory|null $vendor
     */
    public function __construct(TagFactory $vendor = null)
    {
        if ( ! $vendor) {
            $this->tagFactory = new TagFactory();
        } else {
            $this->tagFactory = $vendor;
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

        return PhpDoc::create($syntax->phpdocization(), $this->tagFactory);
    }

    /**
     * @return \Panlatent\Annotation\TagFactory
     */
    public function getTagFactory()
    {
        return $this->tagFactory;
    }

    /**
     * @param \Panlatent\Annotation\TagFactory $tagVendor
     */
    public function setTagFactory($tagVendor)
    {
        $this->tagFactory = $tagVendor;
    }
}