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
use Panlatent\Annotation\PhpDoc;
use Panlatent\Annotation\TagVendor;

class SyntaxAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testPhpdocization()
    {
        $good = require(__DIR__ . '/../../_data/phpdoc_good_expected.php');

        $vendor = new TagVendor();
        $vendor->withDeprecated();
        foreach ($good as $key => $value) {
            $syntax = new SyntaxAnalyzer(new LexicalAnalyzer(new CharacterScanner($value)), $vendor);
            $time = 0;
            foreach ($syntax->phpdocization() as $phpdoc) {
                $this->assertInstanceOf(PhpDoc::class, $phpdoc);
                ++$time;
            }
            $this->assertEquals(1, $time);
        }
    }
}
