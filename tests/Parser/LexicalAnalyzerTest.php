<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Preprocessor;
use Panlatent\Annotation\Parser\SyntaxException;
use Panlatent\Annotation\Parser\Token;
use PHPUnit\Framework\TestCase;

class LexicalAnalyzerTest extends TestCase
{
    public function testTokenization()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_expected.php');
        $expected = require(__DIR__ . '/../_data/lexer_expected.php');

        $lexer = new LexicalAnalyzer();
        foreach ($good as $key => $value) {
            $list = array_reverse($expected[$key]);
            foreach ($lexer->tokenization($value) as $token) {
                $this->assertEquals(array_pop($list), get_class($token), "Content in $key");
            }
            $this->assertEmpty($list, 'Expected tokens remaining or actual tokens missing');
        }
    }

    public function testTokenizationTryException()
    {
        $this->expectException(SyntaxException::class);
        $bad = require(__DIR__ . '/../_data/phpdoc_bad_example.php');

        $content = (new Preprocessor())->preprocessor($bad['tag_end_attach']);
        $lexer = new LexicalAnalyzer();
        foreach ($bad as $key => $value) {
            foreach ($lexer->tokenization($content) as $token) {
                $this->assertInstanceOf(Token::class, $token);
            }
        }
    }
}
