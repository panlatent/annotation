<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Panlatent\Annotation\Parser;
use Panlatent\Annotation\PhpDoc;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParse()
    {
        $good = require(__DIR__ . '/_data/phpdoc_good_example.php');

        $parser = new Parser();
        $parser->getTagVendor()->withDeprecated();
        foreach ($good as $key => $value) {
            $phpdoc = $parser->parser($value);
            $this->assertInstanceOf(PhpDoc::class, $phpdoc);
        }
    }
}
