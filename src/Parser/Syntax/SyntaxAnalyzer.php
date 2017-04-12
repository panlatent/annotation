<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser\Syntax;

use Panlatent\Annotation\Parser\Exception;
use Panlatent\Annotation\Parser\Lexical\LexicalAnalyzer;
use Panlatent\Annotation\Parser\Status as StatusManager;
use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
use Panlatent\Annotation\Parser\Token\InlineEndToken;
use Panlatent\Annotation\Parser\Token\InlineStartToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDescriptionToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagSpecializationToken;
use Panlatent\Annotation\Parser\Token\TagToken;
use Panlatent\Boost\BStack;

class SyntaxAnalyzer
{
    /**
     * @var \Panlatent\Annotation\Parser\Lexical\LexicalAnalyzer
     */
    protected $lexer;

    /**
     * @var \Panlatent\Boost\BStack
     */
    protected $stack;

    /**
     * @var \Panlatent\Annotation\Parser\Status
     */
    protected $status;

    public function __construct(LexicalAnalyzer $lexer)
    {
        $this->lexer = $lexer;
        $this->stack = new BStack();
        $this->status = new StatusManager();
    }

    /**
     * @return array
     * @throws \Panlatent\Annotation\Parser\Exception
     * @throws \Panlatent\Annotation\Parser\Syntax\SyntaxException
     */
    public function phpdocization()
    {
        $phpdoc = $this->getEmptyPhpdoc();
        $tag = $this->getEmptyTag();
        $status = $this->getEmptyStatus();

        foreach ($this->lexer->tokenization() as $token) {
            /** @var \Panlatent\Annotation\Parser\Token $token */
            switch (get_class($token)) {

                case InlineStartToken::class:

                    if ($status->has(Status::SUMMARY)
                        && ! $status->has(Status::DESCRIPTION)) {
                        $this->stash($phpdoc, $tag, $status);
                    } elseif ($status->has(Status::TAG_DETAILS)) {
                        $this->stash($phpdoc, $tag, $status);
                    }

                    continue;

                case  InlineEndToken::class:

                    if ( ! $this->stack->isEmpty()) {
                        if ( ! empty($tag['name'])) {
                            $phpdoc['tags'][] = $tag;
                        }

                        $stash = [$phpdoc, $tag, $status];
                        $this->expose($phpdoc, $tag, $status);
                        if ($status->has(Status::SUMMARY)
                            && ! $status->has(Status::DESCRIPTION)) {
                            $phpdoc['description'] = $stash[0];
                            $status->add(Status::DESCRIPTION);
                        } elseif ($status->has(Status::TAG_DETAILS)) {
                            $tag['description'] = $stash[0];
                        }
                    }

                    continue;

                case SummaryToken::class:

                    $phpdoc['summary'] = $token->value;
                    $status->add(Status::SUMMARY);

                    continue;

                case DescriptionToken::class:

                    $phpdoc['description'] = $token->value;
                    $status->add(Status::DESCRIPTION);

                    continue;

                case TagToken::class:

                    if ( ! empty($tag['name'])) {
                        $phpdoc['tags'][] = $tag;
                    }
                    $tag = $this->getEmptyTag();

                    continue;

                case TagNameToken::class:

                    $tag['name'] = $token->value;

                    continue;

                case TagSpecializationToken::class:

                    $tag['specialization'] = $token->value;

                    continue;

                case TagDetailsToken::class:

                    $status->add(Status::TAG_DETAILS);

                    continue;

                case TagDescriptionToken::class:

                    $tag['description'] = $token->value;

                    continue;

                case FinalToken::class:

                    if ( ! empty($tag['name'])) {
                        $phpdoc['tags'][] = $tag;
                    }
                    return $phpdoc;

                default:

                    throw new SyntaxException('Unexpected token class ' . get_class($token), $token);

            } // The Switch End
        } // The Foreach End

        throw new Exception('Unexpected final token');
    }

    protected function stash(&$phpdoc, &$tag, &$status)
    {
        $this->stack->push([$phpdoc, $tag, $status]);

        $phpdoc = $this->getEmptyPhpdoc();
        $tag = $this->getEmptyTag();
        $status = $this->getEmptyStatus();
    }

    protected function expose(&$phpdoc, &$tag, &$status)
    {
        list($phpdoc, $tag, $status) = $this->stack->pop();
    }

    protected function getEmptyPhpdoc()
    {
        return [
            'summary' => '',
            'description' => '',
            'tags' => []
        ];
    }

    protected function getEmptyTag()
    {
        return [
            'name' => '',
            'specialization' => '',
            'description' => '',
        ];
    }

    protected function getEmptyStatus()
    {
        return new StatusManager();
    }
}