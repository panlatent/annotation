<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Tag;

use Panlatent\Annotation\Parser\PatternMatchFactoryInterface;
use Panlatent\Annotation\TagAbstract;

final class DeprecatedTag extends TagAbstract implements PatternMatchFactoryInterface
{
    protected $name = 'deprecated';

    private $version = '';

    private $description;

    public function __construct($version = null, $description = null)
    {
        parent::__construct();

        $this->version = $version;
        $this->description = $description;
    }

    public static function create($content)
    {
        if (empty($content)) {
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
            $content, $matches)) {
            return new static(null, null);
        }
        return new static($matches[1], isset($matches[2]) ? $matches[2] : '');
    }
}