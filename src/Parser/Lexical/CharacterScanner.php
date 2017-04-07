<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser\Lexical;

use Panlatent\Annotation\Parser\ContextPositionInterface;
use Panlatent\Annotation\Parser\GeneratorInterface;

class CharacterScanner implements GeneratorInterface, ContextPositionInterface
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var \Generator
     */
    protected $generator;

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
        $this->generator = $this->scan();
        $this->length = strlen($content);
    }

    /**
     * @return \Generator
     */
    public function generator()
    {
        return $this->generator;
    }

    /**
     * @return \Generator
     */
    public function scan()
    {
        $this->lineNumber = 1;
        $this->columnNumber = 1;

        for ($this->index = 0; $this->index < $this->length; ++$this->index) {
            $c = $this->content[$this->index];

            yield $c;

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
     * @param bool   $regular
     * @return bool
     */
    public function expected($char, $regular = false)
    {
        if ($this->index + 1 > $this->length) {
            return false;
        } elseif ($regular) {
            return (bool)preg_match($char, substr($this->content, $this->index + 1));
        } elseif (strlen($char) == 1) {
            return isset($this->content[$this->index + 1]) && $char === $this->content[$this->index + 1];
        }
        for ($i = 0; $i < strlen($char); ++$i) {
            if (isset($this->content[$this->index + 1]) && $char[$i] === $this->content[$this->index + 1]){
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $char
     * @param bool   $regular
     * @return bool
     */
    public function trace($char, $regular = false)
    {
        if ($this->index <= 0) {
            return false;
        } elseif ($regular) {
            return (bool)preg_match($char, substr($this->content, 0, $this->index + 1));
        } elseif (strlen($char) == 1){
            return $char === $this->content[$this->index - 1];
        }
        for ($i = 0; $i < strlen($char); ++$i) {
            if ($char[$i] === $this->content[$this->index - 1]){
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $number
     */
    public function back($number = 1)
    {
        $this->index -= $number;
        if ($this->index < 0) {
            $this->index = 0;
        }
    }

    /**
     * @param int $number
     */
    public function skip($number = 1)
    {
        $this->index += $number;
    }

    /**
     * @return string
     */
    public function getChar()
    {
        return $this->content[$this->index];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
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
     * @return string
     */
    public function getPreviousLine()
    {
        $pos = $this->getCurrentLineBorderPos();
        if ($pos[0] <= 0) {
            return '';
        }
        if (false === ($left = strrpos(substr($this->content, 0, $pos[0]), "\n"))) {
            $left = 0;
        } else {
            $left += 1;
        }

        return substr($this->content, $left, $pos[0] - $left);
    }

    /**
     * @return string
     */
    public function getCurrentLine()
    {
        $pos = $this->getCurrentLineBorderPos();

        return substr($this->content, $pos[0] + 1, $pos[1] - $pos[0] - 1);
    }

    /**
     * @return string
     */
    public function getNextLine()
    {
        $pos = $this->getCurrentLineBorderPos();
        if ($pos[1] >= $this->length) {
            $right = $this->length;
        } elseif (false === ($right = strpos($this->content, "\n", $pos[1] + 1))) {
            $right = $this->length;
        }

        return substr($this->content, $pos[1] + 1, $right - $pos[1] - 1);
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return [
            'previous' => $this->getPreviousLine(),
            'current' => $this->getCurrentLine(),
            'next' => $this->getNextLine()
        ];
    }

    /**
     * @return array
     */
    protected function getCurrentLineBorderPos()
    {
        if ($this->index == 0) {
            $left = -1;
        } elseif (false === ($left = strrpos(substr($this->content, 0, $this->index), "\n"))) {
            $left = -1;
        }

        if ($this->index >= $this->length) { // The right is LF position
            $right = $this->length;
        } elseif (false === ($right = strpos($this->content, "\n", $this->index))) {
            $right = $this->length;
        }

        return [$left, $right];
    }
}