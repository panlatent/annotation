<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\ABnfAssess;
use PHPUnit\Framework\TestCase;

class ABnfAssessTest extends TestCase
{
    public function testIsAlpha()
    {
        foreach (str_split('AZaz') as $char) {
            $this->assertTrue(ABnfAssess::isAlpha($char));
        }
        foreach (str_split('09_-') as $char) {
            $this->assertFalse(ABnfAssess::isAlpha($char));
        }
    }
}
