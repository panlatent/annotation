<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Annotation\Parser\Token\TagDetailsToken;

class Tag implements TagInterface, LexicalScanInterface
{
    protected $name;

    protected $description;

    protected $withSignature;

    public function __construct(TagDetailsToken $detailsToken)
    {
        $this->withSignature = false;
        $this->description = $detailsToken->value;
    }

    public function isWithSignature()
    {
        return $this->withSignature;
    }

    public function getName()
    {
        if ( ! empty($this->name)) {
            return $this->name;
        }

        $name = static::class;
        if (0 === strncmp($name, self::DEFINED_TAG_NAMESPACE, strlen(self::DEFINED_TAG_NAMESPACE))) {
            $name = substr($name, strlen(self::DEFINED_TAG_NAMESPACE));
        }

        return $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Panlatent\Annotation\Parser\Token $token
     * @param \Panlatent\Annotation\Parser\CharacterStream $stream
     * @param $stack
     * @param $status
     * @return \Generator
     * @throws \Panlatent\Annotation\Parser\SyntaxException
     */
    public function lexicalScan($token, $stream, $stack, $status)
    {
        for (;$char = $stream->getChar();) {
            switch (get_class($token)) {
                // @todo
            } // The Switch End.
        }
    }
}