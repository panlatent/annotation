<?php

namespace Panlatent\Annotation;

use Panlatent\Boost\ReadOnlyStorage;

class Annotation extends ReadOnlyStorage
{
    protected $title = '';

    /**
     * Annotation constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        parent::__construct();

        $content = $this->getPhpDocStyleContent($content);
        $lines =  $this->splitCleanLine($content);
        $this->setAttributesAndTitle($lines);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    protected function getPhpDocStyleContent($content)
    {
        if ( ! preg_match('#/\*\*\s*\n(.*)\*/#s', $content, $match)) {
            throw new Exception();
        }

        return $match[1];
    }
    
    protected function splitCleanLine($content)
    {
        $content = preg_replace('#^\s*\*?\s*([^\s].*?)$#m', '\1', $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return preg_split('#\s*\n+\s*#', $content);
    }

    protected function setAttributesAndTitle($lines)
    {
        $count = count($lines);
        $lastName = '';
        for ($i = 0; $i < $count; ++$i) {
            $line = $lines[$i];
            if (0 === strncmp($line, '@', 1)) {
                preg_match('#@([a-z][a-zA-Z0-9_-]*)\s*(.*)#', $line, $match);
                $lastName = $match[1];
                $value = $match[2];
                $this->storage[$lastName] = $value;
            } else {
                if (empty($this->storage)) {
                    $this->title .= $line;
                } else {
                    $this->storage[$lastName] .= $line;
                }
            }
        }
    }
}