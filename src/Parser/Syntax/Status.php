<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser\Syntax;

interface Status
{
    const SUMMARY = 1;
    const DESCRIPTION = 2;
    const TAG_DETAILS = 4;
    const TAG_DESCRIPTION = 8;
}