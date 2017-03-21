<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class Token
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var int
     */
    protected $column;

    public function __construct($line = null, $column = null)
    {
        $this->line = $line;
        $this->column = $column;
    }

    public static function factory(array $position)
    {
        return new static($position[0], $position[1]);
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function setPosition($position)
    {
        $this->line = $position[0];
        $this->column = $position[1];
    }
}