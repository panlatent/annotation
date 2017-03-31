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

class Dispatcher
{
    /**
     * @var \Generator
     */
    protected $generator;

    /**
     * @var \Panlatent\Boost\BStack
     */
    protected $receivers;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
        $this->receivers = new BStack();
    }

    public function transfer($receiver)
    {
        $this->receivers->push($receiver);
    }

    public function flush()
    {
        $this->receivers->pop();
    }

    public function handle()
    {
        for ($stream = $this->generator;
             $stream->valid() && $value = $stream->current();
             $stream->next()) {

            $receiver = $this->receivers->top();
            call_user_func($receiver, $value, $stream, $this);
        }

        return $stream->getReturn();
    }
}