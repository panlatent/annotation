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

final class ExampleTag extends Tag
{
    protected $name = 'example';

    private $filePath = '';

    private $fileUri = '';

    private $isUri = false;

    private $startingLine;

    private $lineCount;

    private $description;

    public function __construct($filePath, $fileUri, $startingLine, $lineCount, $description)
    {
        $this->filePath = $filePath;
        $this->fileUri = $fileUri;
        $this->isUri = empty($filePath);
        $this->startingLine = $startingLine;
        $this->lineCount = $lineCount;
        $this->description = $description;
    }

    public static function create(Description $description)
    {
        if (! preg_match('/^(?:\"([^\"]+)\"|(\S+))(?:\s+(.*))?$/sux', $description->getText(), $matches)) {
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