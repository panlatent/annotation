<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Boost\BStack;
use Panlatent\Boost\ObjectStorage;

class Dispatcher
{
    protected $bind = [];

    /**
     * @var \Panlatent\Boost\BStack
     */
    protected $stack;

    public function __construct()
    {
        $this->stack = new BStack();
    }

    public function bind(GeneratorInterface $processor)
    {
        $this->bind[get_class($processor)] = $processor;
    }

    public function call($name)
    {
        if ( !isset($this->bind[$name])) {

        }

        $this->stack->push($this->bind[$name]);
    }

    public function handle()
    {
        $lastReturn = '';
        for (;
            ! $this->stack->isEmpty() &&
            /** @var \Panlatent\Annotation\Parser\GeneratorInterface $top */
            ($top = $this->stack->top()) &&
            ($generator = $top->getGenerator()) &&
            $generator->valid()
        ; $generator->next()) {
            $lastReturn = $generator->current();
        }

        return $lastReturn;
    }
}