<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser\Lexical;

use Panlatent\Annotation\Parser\Lexical\CharacterScanner;
use Panlatent\Annotation\Parser\Lexical\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Preprocessor;
use Panlatent\Annotation\Parser\Syntax\SyntaxException;
use Panlatent\Annotation\Parser\Token;
use PHPUnit\Framework\TestCase;

class LexicalAnalyzerTest extends TestCase
{
    public function testTokenization()
    {
        $good = require(__DIR__ . '/../../_data/phpdoc_good_expected.php');
        $expected = require(__DIR__ . '/../../_data/lexer_expected.php');


        foreach ($good as $key => $value) {
            $list = array_reverse($expected[$key]);
            $lexer = new LexicalAnalyzer(new CharacterScanner($value));
            foreach ($lexer->tokenization() as $token) {
                $this->assertEquals(array_pop($list), get_class($token), "Content in $key");
            }
            $this->assertEmpty($list, 'Expected tokens remaining or actual tokens missing');
        }
    }

    public function testTokenizationTryException()
    {
        $this->expectException(SyntaxException::class);
        $bad = require(__DIR__ . '/../../_data/phpdoc_bad_example.php');

        $content = (new Preprocessor())->preprocessor($bad['tag_end_attach']);
        foreach ($bad as $key => $value) {
            $lexer = new LexicalAnalyzer(new CharacterScanner($content));
            foreach ($lexer->tokenization() as $token) {
                $this->assertInstanceOf(Token::class, $token);
            }
        }
    }
}
