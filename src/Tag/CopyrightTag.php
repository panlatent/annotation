<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Tag;

use Panlatent\Annotation\Description;
use Panlatent\Annotation\Tag;

final class CopyrightTag extends Tag
{
    protected $name = 'copyright';

    public static function create(Description $description)
    {
        return new static($description);
    }
}