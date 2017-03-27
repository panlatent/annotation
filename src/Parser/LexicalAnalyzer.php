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
use Panlatent\Annotation\Parser\Token\InlineEndToken;
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
 * The PHPDoc format has the following ABNF definition:
 * @see https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md#5-the-phpdoc-format
 * ============================================================================
 * PHPDoc             = [summary] [description] [tags]
 * inline-phpdoc      = "{" *SP PHPDoc *SP "}"
 * summary            = *CHAR ("." 1*CRLF / 2*CRLF)
 * description        = 1*(CHAR / inline-tag) 1*CRLF ; any amount of characters
 * ; with inline tags inside
 * tags               = *(tag 1*CRLF)
 * inline-tag         = "{" tag "}"
 * tag                = "@" tag-name [":" tag-specialization] [tag-details]
 * tag-name           = (ALPHA / "\") *(ALPHA / DIGIT / "\" / "-" / "_")
 * tag-specialization = 1*(ALPHA / DIGIT / "-")
 * tag-details        = *SP (SP tag-description / tag-signature / inline-phpdoc)
 * tag-description    = 1*(CHAR / CRLF)
 * tag-signature      = "(" *tag-argument ")"
 * tag-argument       = *SP 1*CHAR [","] *SP
 * ============================================================================
 *
 * @package Panlatent\Annotation\Parser
 */
class LexicalAnalyzer
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

    public function __construct()
    {

    }

    /**
     * @param $docComment
     * @return \Generator
     * @throws \Panlatent\Annotation\Parser\SyntaxException
     */
    public function tokenization($docComment)
    {
        $stream = new CharacterStream($docComment);
        $token = new Token();
        $stack = new BStack();
        $status = new Status();
        $extension = '';

        foreach ($stream->generator() as $char) {
            if ("\0" == $char) {
                break;
            }

            switch (get_class($token)) {

                case Token::class:

                    if (preg_match('#\s#', $char)) {
                        continue;
                    }
                    if ('@' == $char) {
                        $status->add(self::STATUS_TAG);
                        $token = TagToken::factory($stream->getPosition());
                        continue;
                    }
                    if ( ! $status->has(self::STATUS_SUMMARY)) {
                        $status->add(self::STATUS_SUMMARY);
                        $token = SummaryToken::factory($stream->getPosition());
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unknown format', $stream);

                case SummaryToken::class:

                    if ('.' == $char || "\n" == $char) {
                        $token->value .= $char;
                        while ($stream->expected(" \t")) {
                            $stream->skip();
                        }
                        if ($stream->expected("\n")) {
                            yield $token;
                            $token = new DescriptionToken();
                        }
                        continue;
                    }
                    if (1) {
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected summary', $stream);

                case DescriptionToken::class:

                    if ('@' == $char) {
                        yield $token;
                        $token = TagToken::factory($stream->getPosition());
                        continue;
                    }
                    if (1) {
                        if (empty($token->value)) {
                            if (preg_match('#\s#', $char)) {
                                continue;
                            }
                            $token->setPosition($stream->getPosition());
                        }
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected description', $stream);

                case TagToken::class:

                    if (preg_match('#[a-zA-Z\\\\]#', $char)) {
                        yield $token;
                        $token = TagNameToken::factory($stream->getPosition());
                        $token->value .= $char;
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag name', $stream);

                case TagNameToken::class:

                    if (preg_match('#\s#', $char)) {
                        yield $token;
                        $token = new TagDetailsToken();
                        continue;
                    } elseif (preg_match('#[a-zA-Z0-9\_-]#', $char)) {
                        $token->value .= $char;
                        continue;
                    } elseif (':' == $char) {
                        yield $token;
                        $token = new TagSpecializationToken();
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag name', $stream);

                case TagSpecializationToken::class:

                    if (preg_match('#[0-9a-zA-Z-]#', $char)) {
                        $token->value .= $char;
                    } elseif (preg_match('#\s#', $char)) {
                        yield $token;
                        $token = new TagDetailsToken();
                        continue;
                    }

                    throw new SyntaxException('Unexpected tag specialization', $stream);

                case TagDetailsToken::class:

                    if (preg_match('#\s#', $char) && empty($token->value)) {
                        continue;
                    }

                    if (empty($token->value)) {
                        $token->setPosition($stream->getPosition());
                    }

                    if ($receive = yield $token) {
                        if ($receive instanceof LexicalScanInterface) {
                            $extension = $receive->lexicalScan($token, $stream, $stack, $status);
                        } else {
                            // @todo
                        }


                        continue;
                    } else {
                        if ('{' == $char) {
                            $token = InlineStartToken::factory($stream->getPosition()); // A Inline PHPDoc
                            yield $token;
                            $token = new Token();
                            continue;
                        } elseif ('(' == $char) {
                            $token = TagSignatureToken::factory($stream->getPosition());
                            continue;
                        } else {
                            $token = TagDescriptionToken::factory($stream->getPosition());
                            $token->value .= $char;
                            continue;
                        }

                        continue;
                    }
                    // throw new SyntaxException('Unexpected tag details', $stream);

                case TagDescriptionToken::class:

                    if ("\n" == $char) {
                        $token->value .= $char;
                        while ($stream->expected(" \t")) {
                            $stream->skip();
                        }
                        if ($stream->expected('@')) {
                            $stream->skip();
                            yield $token;
                            $token = TagToken::factory($stream->getPosition());
                            continue;
                        }

                        continue;
                    }

                    $token->value .= $char;

                    continue;

                // throw new SyntaxException('Unexpected tag description', $stream);

                case TagSignatureToken::class:

                    throw new SyntaxException('Unexpected tag signature', $stream);

                //case TagInline

                case TagArgumentToken::class:

                    throw new SyntaxException('Unexpected tag argument', $stream);

                default:

                    if (is_object($extension) &&
                        $extension instanceof \Generator &&
                        ! $extension->valid()) {

                        $token = $extension->current();
                        $extension->next();
                    }

                    throw new SyntaxException('Unexpected token class "' . get_class($token) . '"', $stream);

            } // The Switch End.

        } // The Foreach End.

        if ( ! $token instanceof FinalToken) {
            if (get_class($token) != Token::class) {
                yield $token;
            }
            yield FinalToken::factory($stream->getPosition());
        }
    }

    public function evaluator()
    {

    }
}