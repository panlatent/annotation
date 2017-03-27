<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Panlatent\Annotation\Tag\OneTag;
use PHPUnit\Framework\TestCase;
use Tests\_support\OtherTag;

class TagTest extends TestCase
{
    public function testGetName()
    {
        $other = new OtherTag();
        $this->assertEquals('Tests\_support\Other', $other->getName());

        require_once(__DIR__ . '/_support/OneTag.php');
        $one = new OneTag();
        $this->assertEquals('one', $one->getName());
    }
}
