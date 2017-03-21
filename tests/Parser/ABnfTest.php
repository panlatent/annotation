<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Parser;

use Panlatent\Annotation\Parser\ABnf;

class ABnfTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAlpha()
    {
        foreach (str_split('AZaz') as $char) {
            $this->assertTrue(ABnf::isAlpha($char));
        }
        foreach (str_split('09_-') as $char) {
            $this->assertFalse(Abnf::isAlpha($char));
        }
    }
}
