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

final class AuthorTag extends TagAbstract implements PatternMatchFactoryInterface
{
    protected $name = 'author';

    private $authorName = '';

    private $authorEmail = '';

    public function __construct($authorName, $authorEmail)
    {
        parent::__construct();
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
    }

    public static function create($content)
    {
        if ( ! preg_match('/^([^\<]*)(?:\<([^\>]*)\>)?$/u', $content, $matches)) {
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