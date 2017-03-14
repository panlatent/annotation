<?php

namespace Panlatent\Annotation;

class Annotation
{
    protected $title = '';

    protected $attributes = [];

    public function __construct($content)
    {
        $content = $this->getPhpDocStyleContent($content);
        $lines =  $this->splitCleanLine($content);
        $this->setAttributesAndTitle($lines);
    }

    public function get($name)
    {
        return $this->attributes[$name];
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
        $content = preg_replace('#^\s*\*?\s*(.*?)\s*$#m', '\1', $content);
        $content = str_replace(["\n", "\r"], "\n", $content);
        
        return preg_split('#\n+#', $content);
    }

    protected function getPhpDocStyleTitle()
    {
        // @TODO
    }

    protected function setAttributesAndTitle($lines)
    {
        $count = count($lines);
        $lastName = '';
        for ($i = 0; $i < $count; ++$i) {
            $line = $lines[$i];
            if (0 === strncmp($line, '@', 1)) {
                preg_match('#@([a-z][a-zA-Z0-9_-]*)\s*(.*)#', $line, $pMatch);
                $lastName = $name = $pMatch[1];
                $value = $pMatch[2];
                $this->attributes[$name] = $value;
            } else {
                if (empty($this->attributes)) {
                    $this->title .= $line;
                } else {
                    $this->attributes[$lastName] .= $line;
                }
            }
        }
    }
}