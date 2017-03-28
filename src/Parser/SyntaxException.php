<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class SyntaxException extends Exception
{
    protected $syntaxLine;

    protected $syntaxColumn;

    protected $syntaxContext;

    /**
     * SyntaxException constructor.
     *
     * @param string                                               $message
     * @param \Panlatent\Annotation\Parser\SyntaxPositionInterface $position
     * @param int                                                  $code
     * @param \Exception|null                                      $previous
     * @internal param \Panlatent\Annotation\Parser\CharacterStream $stream
     */
    public function __construct($message, SyntaxPositionInterface $position, $code = 0, \Exception $previous = null)
    {
        $this->syntaxLine = $position->getLineNumber();
        $this->syntaxColumn = $position->getColumnNumber();
        $this->syntaxContext = $position->getContext();

        $context = substr($this->syntaxContext['current'], 0, $this->syntaxColumn - 1) . '^' .
            substr($this->syntaxContext['current'], $this->syntaxColumn - 1);
        $message = "$message at Line {$this->syntaxLine}, Column:{$this->syntaxColumn}. The syntax error in context: \"{$context}\"";
        parent::__construct($message, $code, $previous);
    }
}