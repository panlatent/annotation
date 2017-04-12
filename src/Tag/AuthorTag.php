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
use Panlatent\Annotation\TagWithoutNameFactory;

final class AuthorTag extends Tag implements TagWithoutNameFactory
{
    protected $name = 'author';

    private $authorName = '';

    private $authorEmail = '';

    public function __construct($authorName, $authorEmail)
    {
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
    }

    public static function create(Description $description)
    {
        if ( ! preg_match('/^([^\<]*)(?:\<([^\>]*)\>)?$/u', $description->getText(), $matches)) {
            return null;
        }
        $authorName = trim($matches[1]);
        $email = isset($matches[2]) ? trim($matches[2]) : '';

        return new static($authorName, $email);
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }
}