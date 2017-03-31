<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
//use Panlatent\Annotation\Parser\Token\InlineEndToken;
use Panlatent\Annotation\Parser\Token\InlineStartToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagArgumentToken;
use Panlatent\Annotation\Parser\Token\TagDescriptionToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagSignatureToken;
use Panlatent\Annotation\Parser\Token\TagSpecializationToken;
use Panlatent\Annotation\Parser\Token\TagToken;
use Panlatent\Boost\BStack;

/**
 * Class LexicalAnalyzer
 *
 * @package Panlatent\Annotation\Parser
 */
class LexicalAnalyzer implements GeneratorInterface
{
    const STATUS_SPACE = 0;
    const STATUS_PHPDOC = 1;
    const STATUS_INLINE_PHPDOC = 2;
    const STATUS_SUMMARY = 4;
    const STATUS_DESCRIPTION = 8;
    const STATUS_TAGS = 16;
    const STATUS_TAG = 32;
    const STATUS_TAG_NAME = 64;
    const STATUS_TAG_SPECIALIZATION = 128;
    const STATUS_TAG_DETAILS = 256;
    const STATUS_TAG_DESCRIPTION = 512;
    const STATUS_TAG_SIGNATURE = 1024;
    const STATUS_TAG_ARGUMENT = 2048;

    protected $debug = false;

    protected $generateDebugToken = true;

    protected $scanner;

    protected $generator;

    protected $stack;

    protected $status;

    public function __construct(CharacterScanner $scanner)
    {
        $this->scanner = $scanner;
        $this->generator = $this->tokenization();
        $this->stack = new BStack();
        $this->status = new Status();
    }

    /**
     * @return \Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @return \Generator
     * @throws \Panlatent\Annotation\Parser\SyntaxException
     */
    public function tokenization()
    {
        $scanner = $this->scanner;
        $stack = $this->stack;
        $status = $this->status;
        $token = new Token();

        // $scanner = false;

        foreach ($scanner->scan() as $char) {
            if ("\0" == $char) {
                break;
            }

            switch (get_class($token)) {

                case Token::class:

                    if (ABnfAssess::isLWsp($char)) {
                        continue;
                    }
                    if ('@' == $char) {
                        $status->add(self::STATUS_TAG);
                        $token = TagToken::factory($scanner->getPosition());
                        continue;
                    }
                    if ( ! $status->has(self::STATUS_SUMMARY)) {
                        $status->add(self::STATUS_SUMMARY);
                        $token = SummaryToken::factory($scanner->getPosition());
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unknown format', $scanner);

                case SummaryToken::class:

                    if ('.' == $char || "\n" == $char) {
                        $token->value .= $char;
                        while ($scanner->expected(" \t")) {
                            $scanner->skip();
                        }
                        if ($scanner->expected("\n")) {
                            yield $token;
                            $token = new DescriptionToken();
                        }
                        continue;
                    }
                    if (1) {
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected summary', $scanner);

                case DescriptionToken::class:

                    if ('@' == $char) {
                        yield $token;
                        $token = TagToken::factory($scanner->getPosition());
                        continue;
                    }
                    if (1) {
                        if (empty($token->value)) {
                            if (ABnfAssess::isLWsp($char)) {
                                continue;
                            }
                            $token->setPosition($scanner->getPosition());
                        }
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected description', $scanner);

                case TagToken::class:

                    if (ABnfAssess::isAlpha($char) || '\\' == $char) {
                        yield $token;
                        $token = TagNameToken::factory($scanner->getPosition());
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag name', $scanner);

                case TagNameToken::class:

                    if (ABnfAssess::isLWsp($char)) {
                        yield $token;
                        $token = new TagDetailsToken();
                        continue;
                    } elseif (ABnfAssess::isAlpha($char) ||
                        ABnfAssess::isDigit($char) ||
                        '-' == $char ||
                        '_' == $char) {
                        $token->value .= $char;
                        continue;
                    } elseif (':' == $char) {
                        yield $token;
                        $token = new TagSpecializationToken();
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag name', $scanner);

                case TagSpecializationToken::class:

                    if (ABnfAssess::isAlpha($char) ||
                        ABnfAssess::isDigit($char) ||
                        '-' == $char) {
                        $token->value .= $char;
                    } elseif (ABnfAssess::isLWsp($char)) {
                        yield $token;
                        $token = new TagDetailsToken();
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag specialization', $scanner);

                case TagDetailsToken::class:

                    if (ABnfAssess::isLWsp($char) && empty($token->value)) {
                        continue;
                    }

                    if (empty($token->value)) {
                        $token->setPosition($scanner->getPosition());
                    }

                    if ($receive = (yield $token)) {
                        if (is_callable($receive)) {
                            $scanner = call_user_func($receive, $token, $scanner, $stack, $status);
                        } else {
                            // @todo
                        }


                        continue;
                    } else {
                        if ('{' == $char) {
                            $token = InlineStartToken::factory($scanner->getPosition()); // A Inline PHPDoc
                            yield $token;
                            $token = new Token();
                            continue;
                        } elseif ('(' == $char) {
                            $token = TagSignatureToken::factory($scanner->getPosition());
                            continue;
                        } else {
                            $token = TagDescriptionToken::factory($scanner->getPosition());
                            $token->value .= $char;
                            continue;
                        }

                        continue;
                    }
                    // throw new SyntaxException('Unexpected tag details', $scanner);

                case TagDescriptionToken::class:

                    if ($stack->isEmpty() && "\n" == $char) {
                        $token->value .= $char;
                        while ($scanner->expected(" \t")) {
                            $scanner->skip();
                        }
                        if ($scanner->expected('@')) {
                            $scanner->skip();
                            yield $token;
                            $token = TagToken::factory($scanner->getPosition());
                            continue;
                        }

                        continue;
                    } elseif ('{' == $char && ! $scanner->trace('\\')) {
                        $stack->push('{');
                    } elseif ('}' == $char && ! $scanner->trace('\\')) {
                        $stack->pop();
                    }

                    $token->value .= $char;

                    continue;

                // throw new SyntaxException('Unexpected tag description', $scanner);

                case TagSignatureToken::class:

                    throw new SyntaxException('Unexpected tag signature', $scanner);

                //case TagInline

                case TagArgumentToken::class:

                    throw new SyntaxException('Unexpected tag argument', $scanner);

                default:
                    if (is_object($scanner) &&
                        $scanner instanceof \Generator &&
                        ! $scanner->valid()) {

                        $token = $scanner->current();
                        $scanner->next();
                    }

                    throw new SyntaxException('Unexpected token class "' . get_class($token) . '"', $scanner);

            } // The Switch End.

        } // The Foreach End.

        if ( ! $token instanceof FinalToken) {
            if (get_class($token) != Token::class) {
                yield $token;
            }
            yield FinalToken::factory($scanner->getPosition());
        }
    }

    public function evaluator()
    {

    }
}