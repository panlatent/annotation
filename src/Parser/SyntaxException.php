<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Exception;

class SyntaxException extends Exception
{
    protected $syntaxLine;

    protected $syntaxColumn;

    public function __construct($message = "", $line, $column, $code = 0, \Exception $previous = null)
    {
        $message .= ":{$line}:{$column}";
        parent::__construct($message, $code, $previous);
        $this->syntaxLine = $line;
        $this->syntaxColumn = $column;
    }

    public static function factory($message, array $position, $code = 0, \Exception $previous = null)
    {

        return new static($message, $position[0], $position[1], $code, $previous);
    }

}