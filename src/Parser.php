<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\LexicalAnalyzer;

class Parser
{
    public function __construct()
    {

    }

    public function parser($docComment)
    {
        $lexer = new LexicalAnalyzer();
        $docComment = $lexer->preprocessor($docComment);
        $tokenStream = $lexer->tokenization($docComment);
    }
}