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
    public static function create($parser, TagVendor $vendor)
    {
        $storage = [];
        foreach ($parser as $tag) {
            if ($vendor->has($tag['name'], $tag['specialization'])) {
                $class = $vendor->get($tag['name'], $tag['specialization']);
                $storage[] = $class::create(new Description($tag['description']));
            } else {
                /** @var \Panlatent\Annotation\Tag $class */
                $class = $vendor->getDefaultTag();
                $storage[] = $class::createFromName($tag['name'], new Description($tag['description']));
            }
        }

        return new static($storage);
    }
}