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
use Panlatent\Annotation\TagVendor;

class TagVendorTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $vendor = new TagVendor();
        $vendor->register('Panlatent\\Annotation\\AbcTag', 'abc');
        $this->assertEquals('Panlatent\\Annotation\\AbcTag', $vendor->get('abc'));
        $vendor->register('Panlatent\\Annotation\\AbcTag\\NameSpecialization', 'abc', 'name');
        $this->assertEquals('Panlatent\\Annotation\\AbcTag\\NameSpecialization', $vendor->get('abc', 'name'));
    }

    public function testGetThrowException()
    {
        $this->expectException(NotFoundException::class);;

        $vendor = new TagVendor();
        $vendor->get('abc');
    }

    public function testGetThrowExceptionOfDeprecatedTag()
    {
        $this->expectException(NotFoundException::class);;

        $vendor = new TagVendor();
        $vendor->get('link');
    }

    public function testHas()
    {
        $vendor = new TagVendor();
        $vendor->register('Panlatent\\Annotation\\AbcTag', 'abc');
        $this->assertTrue($vendor->has('abc'));
        $this->assertFalse($vendor->has('bcd'));
        $this->assertFalse($vendor->has('abc', 'name'));
    }

    public function testRegister()
    {
        $vendor = new TagVendor(false);
        $vendor->register('Panlatent\\Annotation\\AbcTag', 'abc');
        $this->assertAttributeEquals([
            'abc' => [
                '' => 'Panlatent\\Annotation\\AbcTag',
            ]
        ], 'tags', $vendor);

        $vendor->register('Panlatent\\Annotation\\AbcTag\\NameSpecialization', 'abc', 'name');
        $this->assertAttributeEquals([
            'abc' => [
                '' => 'Panlatent\\Annotation\\AbcTag',
                'name' => 'Panlatent\\Annotation\\AbcTag\\NameSpecialization',
            ]
        ], 'tags', $vendor);

        $vendor->register('Panlatent\\Annotation\\BcdTag', 'bcd');
        $this->assertAttributeEquals([
            'abc' => [
                '' => 'Panlatent\\Annotation\\AbcTag',
                'name' => 'Panlatent\\Annotation\\AbcTag\\NameSpecialization',
            ],
            'bcd' => [
                '' => 'Panlatent\\Annotation\\BcdTag'
            ]
        ], 'tags', $vendor);
    }
}
