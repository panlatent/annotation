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

final class ExampleTag extends TagAbstract implements PatternMatchFactoryInterface
{
    protected $name = 'example';

    private $filePath = '';

    private $isURI = false;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    public static function create($content)
    {
        if (! preg_match('/^(?:\"([^\"]+)\"|(\S+))(?:\s+(.*))?$/sux', $content, $matches)) {
            return null;
        }
        $filePath = null;
        $fileUri  = null;
        if ('' !== $matches[1]) {
            $filePath = $matches[1];
        } else {
            $fileUri = $matches[2];
        }
        $startingLine = 1;
        $lineCount    = null;
        $description  = null;
        // Starting line / Number of lines / Description
        if (preg_match('/^([1-9]\d*)\s*(?:((?1))\s+)?(.*)$/sux', $matches[3], $matches)) {
            $startingLine = (int)$matches[1];
            if (isset($matches[2]) && $matches[2] !== '') {
                $lineCount = (int)$matches[2];
            }
            $description = $matches[3];
        }
        return new static($filePath, $fileUri, $startingLine, $lineCount, $description);
    }
}