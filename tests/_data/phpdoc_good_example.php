<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

$phpdoc = [];

$phpdoc['single_line'] = <<<DOCEND
/** Annotation - Parsing PHPDoc style annotations from comments. */
DOCEND;

$phpdoc['summary_dot'] = <<<DOCEND
/**
 * Annotation - Parsing PHPDoc style annotations from comments.
 * Summary with dot, this is description
 *
 * @var int
 */
DOCEND;

$phpdoc['summary_without_dot'] = <<<DOCEND
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * Summary without dot, this is description
 */
DOCEND;


$phpdoc['with_tags'] = <<<DOCEND
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * This a has summary, description and tags example.
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */
DOCEND;

$phpdoc['inline_basic'] = <<<DOCEND
/**
 * A Basic Inline PHPDoc.
 *
 * {
 *     This is a inline summary of parent description.
 *
 *     This is a inline description of parent description
 *     @return void
 * }
 * 
 * @name {
 *     This is a inline summary of parent tag.
 *     @var int
 * }
 */
DOCEND;

return $phpdoc;