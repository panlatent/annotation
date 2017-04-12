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

final class DeprecatedTag extends Tag
{
    protected $name = 'deprecated';

    private $version = '';

    private $description;

    public function __construct($version = null, $description = null)
    {
        $this->version = $version;
        $this->description = $description;
    }

    public static function create(Description $description)
    {
        if (empty($description->getText())) {
            return new static();
        }
        $matches = [];
        if ( ! preg_match('/^(' . '(?:
            # Normal release vectors.
            \d\S*
            |
            # VCS version vectors. Per PHPCS, they are expected to
            # follow the form of the VCS name, followed by ":", followed
            # by the version vector itself.
            # By convention, popular VCSes like CVS, SVN and GIT use "$"
            # around the actual version vector.
            [^\s\:]+\:\s*\$[^\$]+\$
            )' . ')\s*(.+)?$/sux',
            $description->getText(), $matches)) {
            return new static(null, null);
        }
        return new static($matches[1], isset($matches[2]) ? $matches[2] : '');
    }
}