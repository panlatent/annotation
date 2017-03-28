<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\Exception as ParserException;
use Panlatent\Annotation\Parser\LexicalAnalyzer;
use Panlatent\Annotation\Parser\LexicalScanFactoryInterface;
use Panlatent\Annotation\Parser\PatternMatchFactoryInterface;
use Panlatent\Annotation\Parser\Preprocessor;
use Panlatent\Annotation\Parser\TagFactory;
use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDescriptionToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagToken;

class Parser
{
    protected $tagVendor;

    public function __construct(TagVendor $vendor = null)
    {
        if ( ! $vendor) {
            $this->tagVendor = new TagVendor();
        } else {
            $this->tagVendor = $vendor;
        }
    }

    public function parser($docComment)
    {
        $preprocessor = new Preprocessor();
        if ( ! $preprocessor->check($docComment)) {
            throw new ParserException('DocComment format error');
        }
        $phpdoc = $preprocessor->preprocessor($docComment);
        $factory = new PhpDocFactory();
        $tagFactory = new TagFactory();
        $lexer = new LexicalAnalyzer();

        for ($stream = $lexer->tokenization($phpdoc); $stream->valid() && $token = $stream->current(); $stream->next()) {
            /** @var \Panlatent\Annotation\Parser\Token $token */
            switch (get_class($token)) {
                case SummaryToken::class:

                    $factory->setSummary($token->value);

                    break;

                case DescriptionToken::class:

                    $factory->setDescription($token->value);

                    break;

                case TagToken::class:

                    if ($tagFactory->hasInstance()) {
                        $factory->addTag($tagFactory->getInstance());
                    }
                    $tagFactory = new TagFactory();

                    break;

                case TagNameToken::class:

                    $tagFactory->setName($token->value);

                    break;

                case TagSpecializationInterface::class:

                    $tagFactory->setSpecialization($token->value);

                    break;

                case TagDetailsToken::class:

                    if (false !== ($tagClass = $this->tagVendor->get($tagFactory->getName()
                            , $tagFactory->getSpecialization()))) {
                        $tagFactory->setProduct($tagClass);
                    } else {
                        $tagFactory->setProduct($this->tagVendor->getDefaultTag());
                    }

                    if ($tagFactory->isFactory()) {

                        if ($tagFactory->isFactory(LexicalScanFactoryInterface::class)) {
                            $stream->send($tagFactory->getLexicalScanner());
                        }
//                        elseif ($tagFactory->isFactory(PatternMatchFactoryInterface::class)) {
//                            $tagFactory->create();
//                        }
                    }

                    break;

                case TagDescriptionToken::class:

                    if ($tagFactory->isFactory(PatternMatchFactoryInterface::class)) {
                        /** @var \Panlatent\Annotation\Parser\PatternMatchFactoryInterface $tag */
                        $tagFactory->create();
                        //$tag->($token->value);
                    } else {
                        $tagFactory->setDescription($token->value);
                        $tagFactory->create();
                    }


                    break;

                case FinalToken::class:

                    if ($tagFactory->hasInstance()) {
                        $factory->addTag($tagFactory->getInstance());
                    }
                    break;

                default:

                    throw new ParserException('Unexpected token class ' . get_class($token));

            } // The Switch End
        } // The For End

        return $factory->create();
    }

    /**
     * @return \Panlatent\Annotation\TagVendor
     */
    public function getTagVendor()
    {
        return $this->tagVendor;
    }

    /**
     * @param \Panlatent\Annotation\TagVendor $tagVendor
     */
    public function setTagVendor($tagVendor)
    {
        $this->tagVendor = $tagVendor;
    }
}