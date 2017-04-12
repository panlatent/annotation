<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;


class SlackTag extends Tag implements TagWithNameFactory
{
    public static function createWithName($name, Description $description)
    {
        $tag = new static();
        $tag->name = $name;
        $tag->description = $description;

        return $tag;
    }

    public function getName()
    {
        return $this->name;
    }
}