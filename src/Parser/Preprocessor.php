<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class Preprocessor
{
    protected $keepPosition;

    public function __construct($keepPosition = true)
    {
        $this->keepPosition = $keepPosition;
    }

    public function check($docComment)
    {
        if ( ! preg_match('#/\*\*.*\*/#s', $docComment)) {
            return false;
        }
        if (false !== strpos($docComment, "\r\n")) {
            if ( ! preg_match('#^\s*\*#um', $docComment)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Replace DocComment asterisk for space.
     *
     * @param string $docComment
     * @return string
     */
    public function preprocessor($docComment)
    {
        if (0 === strncmp($docComment, '/**', 3)) { //
            $docComment = substr($docComment, 3);
            if ($this->keepPosition) {
                $docComment = str_repeat(' ', 3) . $docComment;
            } else {
                $docComment = ltrim($docComment);
            }
        }
        if ('*/' == substr($docComment, -2)) {
            $docComment = substr($docComment, 0, -2);
            if ($this->keepPosition) {
                $docComment .= str_repeat(' ', 2);
            } else {
                $docComment = rtrim($docComment);
            }
        }
        if ($this->keepPosition) {
            $phpdoc = preg_replace('#^([ \t]*)\*\*?([ \t]{0,1})#um', '\1 \2', $docComment);
        } else {
            $phpdoc = preg_replace('#^[ \t]*\*\*?[ \t]*#um', '', $docComment);
        }

        return str_replace(["\r", "\r\n"], "\n", $phpdoc);
    }
}