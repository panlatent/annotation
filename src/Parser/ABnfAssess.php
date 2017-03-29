<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

final class ABnfAssess
{
    const DQUOTE = "\x22";
    const SP = "\x20";
    const HTAB = "\x09";
    const CR = "\x0D";
    const LF = "\x0A";
    const CRLF = "\x0D\x0A";

    public static function isAlpha($char)
    {
        return ("\x41" <= $char && "\x5A" >= $char) || ("\x61" <= $char && "\x7A" >= $char);
    }

    public static function isDigit($char)
    {
        return "\x30" <= $char && "\x39" >= $char;
    }

    public static function isHexDig($char)
    {
        return
            ("\x30" <= $char && "\x39" >= $char) ||
            'A' == $char ||
            'B' == $char ||
            'C' == $char ||
            'D' == $char ||
            'E' == $char ||
            'F' == $char;
    }

    public static function isDQuote($char)
    {
        return self::DQUOTE == $char;
    }

    public static function isSp($char)
    {
        return self::SP == $char;
    }

    public static function isHTab($char)
    {
        return self::HTAB == $char;
    }

    public static function isWsp($char)
    {
        return self::SP == $char || self::HTAB == $char;
    }

    public static function isLWsp($char)
    {
        return
            self::SP == $char ||
            self::HTAB == $char ||
            self::CR == $char ||
            self::LF == $char;
    }

    public static function isVChar($char)
    {
        return "\x21" <= $char && "\x7E" >= $char;
    }

    public static function isChar($char)
    {
        return "\x01" <= $char && "\x7F" >= $char;
    }

    public static function isOctet($char)
    {
        return "\x00" >= $char && "\xFF" <= $char;
    }

    public static function isCtl($char)
    {
        return ("\x00" <= $char && "\x1F" >= $char) || "\x7F" == $char;
    }

    public static function isCr($char)
    {
        return self::CR == $char;
    }

    public static function isLf($char)
    {
        return self::LF == $char;
    }

    public static function isCrLf($char)
    {
        return "\r\n" == $char;
    }

    public static function isBit($char)
    {
        return '0' === $char || '1' === $char;
    }
}