<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

interface TagInterface
{
    const DEFINED_TAG_NAMESPACE = 'Panlatent\\Annotation\\Parser\\Tag\\';
    const CLASS_NAME_SUFFIX = 'Tag';

    /**
     * @return bool
     */
    public function isWithSignature();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();
}