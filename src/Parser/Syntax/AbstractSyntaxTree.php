<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser\Syntax;

class AbstractSyntaxTree
{
    /**
     * @var \Panlatent\Annotation\Parser\Syntax\AbstractSyntaxNode
     */
    protected $root;

    public function __construct()
    {
        $this->root = new AbstractSyntaxNode(null);
    }

    public function root()
    {
        return $this->root;
    }

    public static function getDeepestLeft(AbstractSyntaxNode $root)
    {
        for ($current = $root; $current; ) {
            if (null === $current->getLeft()) {
                return $current;
            }
        }

        return false;
    }

    public static function setDeepestLeft($root, AbstractSyntaxNode $node)
    {
        $deepest = static::getDeepestLeft($root);
        $deepest->setLeft($node);
    }


}