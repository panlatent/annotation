<?php
/**
 * Annotation - Parsing PHPDoc style annotations from comments
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/annotation
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Annotation\Parser;

use Panlatent\Annotation\Tag;

class TagFactory
{
    /**
     * @var string
     */
    protected $product;

    /**
     * @var \Panlatent\Annotation\Tag
     */
    protected $instance;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $specialization = '';

    /**
     * @var string
     */
    protected $description = '';


    protected $lexicalScanner;

    /**
     * @return \Panlatent\Annotation\Tag
     */
    public function create()
    {
        if ( ! $this->product) {
            $this->product = Tag::class;
        }

        $product = $this->product;
        if ($this->isFactory(LexicalScanFactoryInterface::class)) {
            /** @var \Panlatent\Annotation\Parser\LexicalScanFactoryInterface $product */
            // return $this->instance = $product::create();
        } elseif ($this->isFactory(PatternMatchFactoryInterface::class)) {
            /** @var \Panlatent\Annotation\Parser\PatternMatchFactoryInterface $product */
            return $this->instance = $product::create($this->description);
        } elseif ($this->isFactory(Tag::class)) {
            /** @var \Panlatent\Annotation\Tag $product */
            return $this->instance = $product::create($this->name, $this->description);
        }

        return $this->instance = new $product();
    }

    public function getLexicalScanner()
    {
        return $this->lexicalScanner;
    }

    /**
     * @return \Panlatent\Annotation\Tag
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return bool
     */
    public function hasInstance()
    {
        return ! empty($this->instance);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->name);
    }

    public function isFactory($factory = null)
    {
        if ( ! $factory) {
            $factory = AbstractFactoryInterface::class;
        }

        return is_subclass_of($this->product, $factory);
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setProduct($className)
    {
        $this->product = $className;

        if ($this->isFactory(LexicalScanFactoryInterface::class)) {
            /** @var \Panlatent\Annotation\Parser\LexicalScanFactoryInterface $className */
            $this->lexicalScanner = [$className, 'create'];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * @param string $specialization
     * @return $this
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}