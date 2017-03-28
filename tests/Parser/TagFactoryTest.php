<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests\Parser;

use Panlatent\Annotation\Parser\PatternMatchFactoryInterface;
use Panlatent\Annotation\Parser\TagFactory;
use Panlatent\Annotation\Tag\ApiTag;
use Panlatent\Annotation\Tag\AuthorTag;
use PHPUnit\Framework\TestCase;

class TagFactoryTest extends TestCase
{
    public function testIsFactory()
    {
        $factory = new TagFactory();
        $factory->setProduct(AuthorTag::class);
        $this->assertTrue($factory->isFactory());
        $this->assertTrue($factory->isFactory(PatternMatchFactoryInterface::class));
        $factory->setProduct(ApiTag::class);
        $this->assertFalse($factory->isFactory());
    }
}
