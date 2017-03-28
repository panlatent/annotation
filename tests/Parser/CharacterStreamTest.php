<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\CharacterStream;
use PHPUnit\Framework\TestCase;

class CharacterStreamTest extends TestCase
{
    public function testGenerator()
    {
        $actual = '';
        $stream = new CharacterStream('HelloWorld');
        foreach ($stream->generator() as $char) {
            $actual .= $char;
        }
        $this->assertEquals('HelloWorld', substr($actual, 0, -1));
        $this->assertEquals("\0", substr($actual, -1));
    }

    public function testGetPosition()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_example.php');
        foreach ($good as $key => $value) {
            $i = 0;
            $stream = new CharacterStream($value);
            $value .= "\0";
            foreach ($stream->generator() as $char) {
                $this->assertEquals($value[$i++], $char);
            }
        }
    }

    /**
     * @depends testGetPosition
     */
    public function testGetContext()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_example.php');
        foreach ($good as $key => $value) {
            $stream = new CharacterStream($value);
            $lines = explode("\n", $value);
            is_string($lines) and $lines = [$lines];
            for ($i = 0; $i < count($lines); ++$i) {
                for ($j = 0; $j <= strlen($lines[$i]); ++$j) { // include a LF, so has =
                    $this->assertEquals([
                        'previous' => $i == 0 ? '' : $lines[$i - 1],
                        'current' => $lines[$i],
                        'next' => $i >= count($lines) - 1  ? '' : $lines[$i + 1]
                    ], $stream->getContext(), "$key at $i:$j");
                    $stream->skip();
                }
            }
        }

    }

}
