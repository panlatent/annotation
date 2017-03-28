<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

interface SyntaxPositionInterface
{
    public function getPosition();

    public function getLineNumber();

    public function getColumnNumber();

    public function getContext();
}