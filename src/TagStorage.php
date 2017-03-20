<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Tag\AuthorTag;
use Panlatent\Annotation\Tag\CopyrightTag;
use Panlatent\Annotation\Tag\VersionTag;
use Panlatent\Boost\ObjectStorage;

class TagStorage extends ObjectStorage
{
    public function __construct()
    {
        $this->attach(new VersionTag());
        $this->attach(new AuthorTag());
        $this->attach(new CopyrightTag());
    }
}