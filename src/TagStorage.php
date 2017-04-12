<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Boost\Storage;

class TagStorage extends Storage
{
    public static function create($parser, TagFactory $factory)
    {
        $storage = [];
        foreach ($parser as $tag) {
            $factory->create($tag['name'], new Description($tag['description']));
        }

        return new static($storage);
    }
}