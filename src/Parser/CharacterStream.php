<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class CharacterStream implements SyntaxPositionInterface
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @var int
     */
    protected $lineNumber = 1;

    /**
     * @var int
     */
    protected $columnNumber = 1;

    /**
     * CharacterStream constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
        $this->length = strlen($content);
    }

    /**
     * @return \Generator
     */
    public function generator()
    {
        $this->lineNumber = 1;
        $this->columnNumber = 1;

        for ($this->index = 0; $this->index < $this->length; ++$this->index) {
            $c = $this->content[$this->index];
            if (yield $c) {
                //yield [$lineNumber, $columnNumber];
            }

            if ("\n" == $c) {
                ++$this->lineNumber;
                $this->columnNumber = 1;
            } else {
                ++$this->columnNumber;
            }
        }

        yield "\0";
    }

    /**
     * @param string $char
     * @param bool  $regular
     * @return bool
     */
    public function expected($char, $regular = false)
    {
        if ($this->index + 1 > $this->length) {
            return false;
        } elseif ($regular) {
            return (bool)preg_match($regular, $char);
        } elseif (strlen($char) == 1) {
            return $char === $this->content[$this->index + 1];
        }
        for ($i = 0; $i < strlen($char); ++$i) {
            if ($char[$i] === $this->content[$this->index + 1]){
                return true;
            }
        }

        return false;
    }

    public function trace()
    {

    }

    public function back()
    {

    }

    public function skip($number = 1)
    {
        $this->index += $number;
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

    public function getPosition()
    {
        return [$this->lineNumber, $this->columnNumber];
    }

    public function getContext()
    {
        if (false != ($left = strrpos($this->content, "\n", $this->index))) {
            if (false != ($lastLeft = strrpos($this->content, "\n", $left))) {
                $last = substr($this->content, $lastLeft, $left);
            }
        } else {
            $left = 0;
        }
        if (false != ($right = strpos($this->content, "\n", $this->index))) {
            if (false != ($nextRight = strpos($this->content, "\n", $right))) {
                $next = substr($this->content, $right, $nextRight);
            }
        } else {
            $right = strlen($this->content);
        }
        $current = substr($this->content, $left, $right - $left);

        return compact('last', 'current', 'next');
    }
}