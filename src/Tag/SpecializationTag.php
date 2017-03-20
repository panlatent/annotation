<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Tag;

use Panlatent\Annotation\Tag;

class SpecializationTag extends Tag // 专业化标签 @see:unit-test \Mapping\EntityTest::testGetId
{
    protected $specialization;
}