<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Annotation\Parser\LexicalScanFactoryInterface;
use Panlatent\Annotation\Parser\PatternMatchFactoryInterface;
use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDescriptionToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagToken;
use Panlatent\Annotation\PhpDocFactory;
use Panlatent\Annotation\TagSpecializationInterface;
use Panlatent\Annotation\TagVendor;

class SyntaxAnalyzer implements GeneratorInterface
{
    protected $lexer;

    protected $generator;

    protected $phpdocFactory;

    protected $tagFactory;

    protected $tagVendor;

    public function __construct(LexicalAnalyzer $lexer, TagVendor $tagVendor)
    {
        $this->lexer = $lexer;
        $this->tagVendor = $tagVendor;
        $this->generator = $this->phpdocization();
        $this->phpdocFactory = new PhpDocFactory();
        $this->tagFactory = new TagFactory();
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
    public function phpdocization()
    {
        foreach ($this->lexer->tokenization() as $token) {
            /** @var \Panlatent\Annotation\Parser\Token $token */
            switch (get_class($token)) {
                case SummaryToken::class:

                    $this->phpdocFactory->setSummary($token->value);

                    break;

                case DescriptionToken::class:

                    $this->phpdocFactory->setDescription($token->value);

                    break;

                case TagToken::class:

                    if ($this->tagFactory->hasInstance()) {
                        $this->phpdocFactory->addTag($this->tagFactory->getInstance());
                    }
                    $this->tagFactory = new TagFactory();

                    break;

                case TagNameToken::class:

                    $this->tagFactory->setName($token->value);

                    break;

                case TagSpecializationInterface::class:

                    $this->tagFactory->setSpecialization($token->value);

                    break;

                case TagDetailsToken::class:

                    if (false !== ($tagClass = $this->tagVendor->get($this->tagFactory->getName()
                            , $this->tagFactory->getSpecialization()))
                    ) {
                        $this->tagFactory->setProduct($tagClass);
                    } else {
                        $this->tagFactory->setProduct($this->tagVendor->getDefaultTag());
                    }

                    if ($this->tagFactory->isFactory()) {
                        if ($this->tagFactory->isFactory(LexicalScanFactoryInterface::class)) {
                            $tagScanner = $this->tagFactory->getLexicalScanner();
                            $tag = call_user_func($tagScanner);
//                        $dispatcher->transfer(); // @todo
//                        $stream->send($this->tagFactory->getLexicalScanner());
                        } elseif ($this->tagFactory->isFactory(PatternMatchFactoryInterface::class)) {
                            $tag = $this->tagFactory->create();
                        }
                    }

                    break;

                case TagDescriptionToken::class:

                    if ($this->tagFactory->isFactory(PatternMatchFactoryInterface::class)) {
                        /** @var \Panlatent\Annotation\Parser\PatternMatchFactoryInterface $tag */
                        $this->tagFactory->create();
                        //$tag->($token->value);
                    } else {
                        $this->tagFactory->setDescription($token->value);
                        $this->tagFactory->create();
                    }


                    break;

                case FinalToken::class:

                    if ($this->tagFactory->hasInstance()) {
                        $this->phpdocFactory->addTag($this->tagFactory->getInstance());
                    }
                    break;

                default:

                    throw new SyntaxException('Unexpected token class ' . get_class($token), $token);

            } // The Switch End
        } // The Foreach End

        yield $this->phpdocFactory->create();
    }
}