<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Parser;

use Panlatent\Annotation\Parser\LexicalAnalyzer;
use Panlatent\Annotation\Parser\SyntaxException;
use Panlatent\Annotation\Parser\Token;

class LexicalAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    protected $good;

    protected $bad;

    protected function setUp()
    {
        $this->good = require(__DIR__ . '/../_data/phpdoc_good_example.php');
        $this->bad = require(__DIR__ . '/../_data/phpdoc_bad_example.php');
    }

    public function testPreprocessor()
    {
        $lexer = new LexicalAnalyzer();
        $this->assertEquals("    Annotation - Parsing PHPDoc style annotations from comments.   ",
            $lexer->preprocessor($this->good['single_line']));
        $this->assertEquals("   \n   Annotation - Parsing PHPDoc style annotations from comments.\n   Summary with dot, this is description\n  \n   @var int\n   ",
            $lexer->preprocessor($this->good['summary_dot']));
        $this->assertEquals("   \n   Annotation - Parsing PHPDoc style annotations from comments\n  \n   Summary without dot, this is description\n   ",
            $lexer->preprocessor($this->good['summary_without_dot']));
    }

    public function testPreprocessorWithoutPosition()
    {
        $lexer = new LexicalAnalyzer();
        $this->assertEquals("Annotation - Parsing PHPDoc style annotations from comments.",
            $lexer->preprocessor($this->good['single_line'], false));
        $this->assertEquals("Annotation - Parsing PHPDoc style annotations from comments.\nSummary with dot, this is description\n\n@var int",
            $lexer->preprocessor($this->good['summary_dot'], false));
        $this->assertEquals("Annotation - Parsing PHPDoc style annotations from comments\n\nSummary without dot, this is description",
            $lexer->preprocessor($this->good['summary_without_dot'], false));
    }

    public function testTokenization()
    {
        $lexer = new LexicalAnalyzer();
        $content = $lexer->preprocessor($this->good['summary_dot']);
        foreach ($lexer->tokenization($content) as $token) {
            $this->assertInstanceOf(Token::class, $token);
        }
    }

    public function testTokenizationTryException()
    {
        $this->setExpectedException(SyntaxException::class);

        $lexer = new LexicalAnalyzer();
        $content = $lexer->preprocessor($this->bad['tag_end_attach']);
        foreach ($lexer->tokenization($content) as $token) {
            $this->assertInstanceOf(Token::class, $token);
        }
    }

    public function testGenerateCharacterStream()
    {
        $actual = '';
        foreach (LexicalAnalyzer::generateCharacterStream('HelloWorld') as $char) {
            $actual .= $char;
        }
        $this->assertEquals('HelloWorld', $actual);
    }
}
