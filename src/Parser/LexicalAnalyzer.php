<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagToken;

/**
 * Class LexicalAnalyzer
 *
 * The PHPDoc format has the following ABNF definition:
 * @see https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md#5-the-phpdoc-format
 * ============================================================================
 * PHPDoc             = [summary] [description] [tags]
 * inline-phpdoc      = "{" *SP PHPDoc *SP "}"
 * summary            = *CHAR ("." 1*CRLF / 2*CRLF)
 * description        = 1*(CHAR / inline-tag) 1*CRLF ; any amount of characters
 * ; with inline tags inside
 * tags               = *(tag 1*CRLF)
 * inline-tag         = "{" tag "}"
 * tag                = "@" tag-name [":" tag-specialization] [tag-details]
 * tag-name           = (ALPHA / "\") *(ALPHA / DIGIT / "\" / "-" / "_")
 * tag-specialization = 1*(ALPHA / DIGIT / "-")
 * tag-details        = *SP (SP tag-description / tag-signature / inline-phpdoc)
 * tag-description    = 1*(CHAR / CRLF)
 * tag-signature      = "(" *tag-argument ")"
 * tag-argument       = *SP 1*CHAR [","] *SP
 * ============================================================================
 *
 * @package Panlatent\Annotation\Parser
 */
class LexicalAnalyzer
{
    const STATUS_SPACE = 0;
    const STATUS_PHPDOC = 1;
    const STATUS_INLINE_PHPDOC = 2;
    const STATUS_SUMMARY = 4;
    const STATUS_DESCRIPTION = 8;
    const STATUS_TAGS = 16;
    const STATUS_TAG = 32;
    const STATUS_TAG_NAME = 64;
    const STATUS_TAG_SPECIALIZATION = 128;
    const STATUS_TAG_DETAILS = 256;
    const STATUS_TAG_DESCRIPTION = 512;
    const STATUS_TAG_SIGNATURE = 1024;
    const STATUS_TAG_ARGUMENT = 2048;

    protected $debug = false;

    protected $generateDebugToken = true;

    public function __construct()
    {
    }

    public static function generateCharacterStream($string)
    {
        $length = strlen($string);
        $lineNumber = 1;
        $columnNumber = 1;
        for ($i = 0; $i < $length; ++$i) {
            $c = $string[$i];
            if (yield $c) {
                yield [$lineNumber, $columnNumber];
            }

            if ("\n" == $c) {
                ++$lineNumber;
                $columnNumber = 1;
            } else {
                ++$columnNumber;
            }
        }
    }

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

    /**
     * @param $docComment
     * @return \Generator
     * @throws \Panlatent\Annotation\Parser\SyntaxException
     */
    public function tokenization($docComment)
    {
        $stream = $this->generateCharacterStream($docComment);
        $status = new Status();
        $token = new Token();
        for (; ! $token instanceof FinalToken
                && $stream->valid(); $stream->next()) {
            $c = $stream->current();
            switch (get_class($token)) {
                case Token::class:
                    if (preg_match('#\s#', $c)) {
                        continue;
                    }
                    if ('@' == $c) {
                        $status->add(self::STATUS_TAG);
                        $token = TagToken::factory($stream->send(true));
                        continue;
                    }
                    if ("\0" != $c && ! $status->has(self::STATUS_SUMMARY)) {
                        $status->add(self::STATUS_SUMMARY);
                        $token = SummaryToken::factory($stream->send(true));
                        $token->value .= $c;
                        continue;
                    }
                    throw SyntaxException::factory('Unknown format', $stream->send(true));
                case SummaryToken::class:
                    if ('.' == $c || "\n" == $c) {
                        $token->value .= $c;
                        $stream->next();
                        for ($nc = $stream->current();
                             preg_match('#[ \t]#', $nc);
                             $nc = $stream->current()) { // Skip space
                            $stream->next();
                        }
                        if ("\n" == $nc) {
                            yield $token;
                            $token = new DescriptionToken();
                        }
                        continue;
                    }
                    if ("\0" != $c) {
                        $token->value .= $c;
                        continue;
                    }
                    throw SyntaxException::factory('Unexpected summary', $stream->send(true));
                case DescriptionToken::class:
                    // if ("\n" == $c) @todo
                    if ('@' == $c) {
                        yield $token;
                        $token = TagToken::factory($stream->send(true));
                        continue;
                    }
                    if ("\0" != $c) {
                        if (empty($token->value)) {
                            if (preg_match('#\s#', $c)) {
                                continue;
                            }
                            $token->setPosition($stream->send(true));
                        }
                        $token->value .= $c;
                        continue;
                    }

                    throw SyntaxException::factory('Unexpected description', $stream->send(true));
                case TagToken::class:
                    if (preg_match('#[a-zA-Z\\\\]#', $c)) {
                        yield $token;
                        $token = TagNameToken::factory($stream->send(true));
                        $token->value .= $c;
                        continue;
                    }
                    throw SyntaxException::factory('Unexpected tag name', $stream->send(true));
                case TagNameToken::class:
                    if (preg_match('#\s#', $c)) {
                        yield $token;
                        $token = new TagDetailsToken(); // @todo
                        continue;
                    } elseif (preg_match('#[a-zA-Z0-9\_-]#', $c)) {
                        $token->value .= $c;
                        continue;
                    } elseif (':' == $c) {
                        $token = new Token(); // @todo
                        continue;
                    }
                    throw SyntaxException::factory('Unexpected tag name', $stream->send(true));
                case TagDetailsToken::class: //@todo
                    if (preg_match('#\s#', $c) && empty($token->value)) {
                        continue;
                    }
                    if ("\n" == $c) {
                        yield $token;
                        $token = new Token();
                        continue;
                    }
                    if ("\0" != $c) {
                        if (empty($token->value)) {
                            $token->setPosition($stream->send(true));
                        }
                        $token->value .= $c;
                    }
                    break;
            }
        }
    }

    public function evaluator()
    {

    }


}