<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Panlatent\Annotation\Tag\StandardTag;
use PHPUnit\Framework\TestCase;
use Tests\_support\ExternalTag;

class TagTest extends TestCase
{
    public function testGetName()
    {
        $other = new ExternalTag();
        $this->assertEquals('Tests\_support\External', $other->getName());

        require_once(__DIR__ . '/_support/StandardTag.php');
        $one = new StandardTag();
        $this->assertEquals('standard', $one->getName());
    }
}
