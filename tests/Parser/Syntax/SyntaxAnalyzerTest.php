<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser\Syntax;

use Panlatent\Annotation\Parser\Lexical\CharacterScanner;
use Panlatent\Annotation\Parser\Lexical\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Syntax\SyntaxAnalyzer;

class SyntaxAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testPhpdocization()
    {
        $good = require(__DIR__ . '/../../_data/phpdoc_good_expected.php');

        foreach ($good as $key => $value) {
            $syntax = new SyntaxAnalyzer(new LexicalAnalyzer(new CharacterScanner($value)));
            $this->assertNotEmpty($syntax->phpdocization());
        }
    }

    public function testPhpdocizationUsingInline()
    {
        $inline = require(__DIR__ . '/../../_data/phpdoc_good_expected.php');
        $syntax = new SyntaxAnalyzer(new LexicalAnalyzer(new CharacterScanner($inline['inline_basic'])));
        // var_export($syntax->phpdocization());
    }
}
