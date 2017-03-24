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
    public function check($docComment)
    {
        if ( ! preg_match('#/\*\*.*\*/#s', $docComment)) {
            throw new Exception('Bad DocComment');
        }
        if (false !== strpos($docComment, "\r\n")) {
            if ( ! preg_match('#^\s*\*#um', $docComment)) {
                throw new Exception('Bad multi line DocComment');
            }
        }
    }

    /**
     * Replace DocComment asterisk for space.
     *
     * @param string $docComment
     * @param bool   $keepPosition
     * @return string
     */
    public function preprocessor($docComment, $keepPosition = true)
    {
        if (0 === strncmp($docComment, '/**', 3)) { //
            $docComment = substr($docComment, 3);
            if ($keepPosition) {
                $docComment = str_repeat(' ', 3) . $docComment;
            } else {
                $docComment = ltrim($docComment);
            }
        }
        if ('*/' == substr($docComment, -2)) {
            $docComment = substr($docComment, 0, -2);
            if ($keepPosition) {
                $docComment .= str_repeat(' ', 2);
            } else {
                $docComment = rtrim($docComment);
            }
        }
        if ($keepPosition) {
            $docComment = preg_replace('#^([ \t]*)\*\*?([ \t]{0,1})#um', '\1 \2', $docComment);
        } else {
            $docComment = preg_replace('#^[ \t]*\*\*?[ \t]{0,1}#um', '', $docComment);
        }

        return str_replace(["\r", "\r\n"], "\n", $docComment);
    }
}