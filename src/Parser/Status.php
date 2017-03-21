<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

class Status
{
    protected $status;

    public function __construct($init = 0)
    {
        $this->status = $init;
    }

    public function add($status)
    {
        $this->status |= $status;
    }

    public function has($status)
    {
        return (bool)($this->status & $status);
    }

    public function remove($status)
    {
        $this->status ^= $status;
    }

    public function set($status)
    {
        $this->status = $status;
    }
}