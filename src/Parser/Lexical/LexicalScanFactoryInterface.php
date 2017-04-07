<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser\Lexical;

interface LexicalScanFactoryInterface extends AbstractFactoryInterface
{
    /**
     * @param $token
     * @param $stream
     * @param $stack
     * @param $status
     * @return \Generator
     */
    public static function create($token, $stream, $stack, $status);
}