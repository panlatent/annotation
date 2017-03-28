<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

class Annotation
{
    /**
     * @var \Panlatent\Annotation\Parser
     */
    protected $parser;

    public function __construct(Parser $parser = null)
    {
        if ( ! $parser) {
            $parser = new Parser();
        }
        $this->parser = $parser;
    }

    /**
     * @return \Panlatent\Annotation\Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param \Panlatent\Annotation\Parser $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }
}