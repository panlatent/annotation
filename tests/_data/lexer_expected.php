<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

use Panlatent\Annotation\Parser\Token\DescriptionToken;
use Panlatent\Annotation\Parser\Token\FinalToken;
use Panlatent\Annotation\Parser\Token\InlineEndToken;
use Panlatent\Annotation\Parser\Token\InlineStartToken;
use Panlatent\Annotation\Parser\Token\SummaryToken;
use Panlatent\Annotation\Parser\Token\TagDescriptionToken;
use Panlatent\Annotation\Parser\Token\TagDetailsToken;
use Panlatent\Annotation\Parser\Token\TagNameToken;
use Panlatent\Annotation\Parser\Token\TagToken;

$lexer['single_line'] = [
    SummaryToken::class,
    FinalToken::class,
];

$lexer['summary_dot'] = [
    SummaryToken::class,
    DescriptionToken::class,
    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,
    FinalToken::class,
];

$lexer['summary_without_dot'] = [
    SummaryToken::class,
    DescriptionToken::class,
    FinalToken::class,
];

$lexer['with_tags'] = [
    SummaryToken::class,
    DescriptionToken::class,

    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,

    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,

    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,

    FinalToken::class,
];

$lexer['inline_basic'] = [
    SummaryToken::class,

    InlineStartToken::class,
    SummaryToken::class,
    DescriptionToken::class,
    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,
    InlineEndToken::class,

    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,

    InlineStartToken::class,
    SummaryToken::class,
    TagToken::class,
    TagNameToken::class,
    TagDetailsToken::class,
    TagDescriptionToken::class,
    InlineEndToken::class,

    FinalToken::class,
];

return $lexer;
