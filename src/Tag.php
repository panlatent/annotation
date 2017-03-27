<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation;

class Tag implements TagInterface
{
    const DEFAULT_NAMESPACE = 'Panlatent\\Annotation\\Tag\\';
    const CLASS_NAME_SUFFIX = 'Tag';

    protected $name;

    protected $specialization;

    protected $details;

    public function __construct($specialization = null, $details = null)
    {
        $this->specialization = $specialization;
        $this->details = $details;
    }

    public function getName()
    {
        if (empty($this->name)) {
            $tagName = get_called_class();
            if (self::CLASS_NAME_SUFFIX == substr($tagName, -strlen(self::CLASS_NAME_SUFFIX))) {
                $tagName = substr($tagName, 0, -strlen(self::CLASS_NAME_SUFFIX));
            }
            if (0 === strncmp($tagName, self::DEFAULT_NAMESPACE, strlen(self::DEFAULT_NAMESPACE))) {
                $tagName = strtolower(substr($tagName, strlen(self::DEFAULT_NAMESPACE)));
            }
            $this->name = $tagName;
        }

        return $this->name;
    }
}