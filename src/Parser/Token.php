<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class Token implements ContextPositionInterface
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @var int
     */
    protected $columnNumber;

    /**
     * Token constructor.
     *
     * @param int $line
     * @param int $column
     */
    public function __construct($line = null, $column = null)
    {
        $this->lineNumber = $line;
        $this->columnNumber = $column;
    }

    /**
     * @param array $position
     * @return static
     */
    public static function factory(array $position)
    {
        return new static($position[0], $position[1]);
    }

    /**
     * @todo
     * @return string
     */
    public function getContext()
    {
        return '';
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @return int
     */
    public function getColumnNumber()
    {
        return $this->columnNumber;
    }

    /**
     * @return array
     */
    public function getPosition()
    {
        return [$this->lineNumber, $this->columnNumber];
    }

    /**
     * @param array $position
     */
    public function setPosition(array $position)
    {
        $this->lineNumber = $position[0];
        $this->columnNumber = $position[1];
    }
}