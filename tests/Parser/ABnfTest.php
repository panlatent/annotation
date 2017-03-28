<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\ABnf;
use PHPUnit\Framework\TestCase;

class ABnfTest extends TestCase
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
