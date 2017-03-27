<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

use Panlatent\Annotation\Parser\Exception;
use Panlatent\Annotation\Parser\LexicalAnalyzer;
use Panlatent\Annotation\Parser\LexicalScanInterface;
use Panlatent\Annotation\Parser\Preprocessor;
use Panlatent\Annotation\Parser\TagSpecializationInterface;
use Panlatent\Annotation\Parser\TagVendor;
use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagToken;

class Parser
{
    protected $tagVendor;

    public function __construct()
    {
        $this->tagVendor = new TagVendor();
    }

    public function parser($docComment)
    {
        $preprocessor = new Preprocessor();
        if ( ! $preprocessor->check($docComment)) {
            throw new Exception('DocComment format error');
        }
        $phpdoc = $preprocessor->preprocessor($docComment);
        $factory = new PhpDocFactory();
        $lexer = new LexicalAnalyzer();
        $tag = [];
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

                    $tag = [
                        'name' => '',
                        'specialization' => '',
                    ];

                    break;

                case TagNameToken::class:

                    if (false === ($tagClass = $this->tagVendor->get($token->value))) {
                        $tag['name'] = $this->tagVendor->getDefault();
                    } else {
                        $tag['name'] = $tagClass;
                    }

                    break;

                case TagSpecializationInterface::class:

                    $tag['specialization'] = $token->value;

                    break;

                case TagDetailsToken::class:

                    $t = new $tag['class'];
                    if ($tag instanceof LexicalScanInterface) {

                        $stream->send($tag);
                    } else {

                    }
                    break;

            }
        }
    }
}