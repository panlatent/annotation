<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

interface TagInterface
{
    const SIGN = '@';

    /**
     * @return bool
     */
    //public function isWithSignature();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    //public function getDescription();
}