<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Panlatent\Annotation\NotFoundException;
use Panlatent\Annotation\TagFactory;

class TagFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $vendor = new TagFactory();
        $vendor->register('abc', 'Panlatent\\Annotation\\AbcTag');
        $this->assertEquals('Panlatent\\Annotation\\AbcTag', $vendor->get('abc'));
        $vendor->register('abc:name', 'Panlatent\\Annotation\\AbcNameTag');
        $this->assertEquals('Panlatent\\Annotation\\AbcNameTag', $vendor->get('abc:name'));
    }

    public function testGetThrowException()
    {
        $this->expectException(NotFoundException::class);;

        $vendor = new TagFactory();
        $vendor->get('abc');
    }

    public function testGetThrowExceptionOfDeprecatedTag()
    {
        $this->expectException(NotFoundException::class);;

        $vendor = new TagFactory();
        $vendor->withoutDeprecatedTags();
        $vendor->get('link');
    }

    public function testHas()
    {
        $vendor = new TagFactory();
        $vendor->register('abc', 'Panlatent\\Annotation\\AbcTag');
        $this->assertTrue($vendor->has('abc'));
        $this->assertFalse($vendor->has('bcd'));
        $this->assertFalse($vendor->has('abc:name'));
    }

    public function testRegister()
    {
        $vendor = new TagFactory(false);
        $vendor->withoutStandardTags();
        $vendor->withoutDeprecatedTags();
        $vendor->register('abc', 'Panlatent\\Annotation\\AbcTag');
        $this->assertAttributeEquals([
            'abc' => 'Panlatent\\Annotation\\AbcTag',
        ], 'availableTags', $vendor);

        $vendor->register('abc:name', 'Panlatent\\Annotation\\AbcNameTag');
        $this->assertAttributeEquals([
            'abc'      => 'Panlatent\\Annotation\\AbcTag',
            'abc:name' => 'Panlatent\\Annotation\\AbcNameTag',
        ], 'availableTags', $vendor);
    }
}
