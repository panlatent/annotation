<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\Preprocessor;
use PHPUnit\Framework\TestCase;

class PreprocessorTest extends TestCase
{
    public function testCheck()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_example.php');

        $preprocessor = new Preprocessor();
        foreach ($good as $key => $value) {
            $this->assertTrue($preprocessor->check($value));
        }
    }


    public function testPreprocessor()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_example.php');
        $expected = require(__DIR__ . '/../_data/phpdoc_good_expected.php');

        $preprocessor = new Preprocessor();
        foreach ($good as $key => $value) {
            $this->assertEquals($expected[$key], $preprocessor->preprocessor($value));
        }
    }

    public function testPreprocessorNotKeepPosition()
    {
        $good = require(__DIR__ . '/../_data/phpdoc_good_example.php');
        $expected = require(__DIR__ . '/../_data/phpdoc_good_expected_not_keep.php');

        $preprocessor = new Preprocessor(false);
        foreach ($good as $key => $value) {
            $this->assertEquals($expected[$key], $preprocessor->preprocessor($value));
        }
    }
}
