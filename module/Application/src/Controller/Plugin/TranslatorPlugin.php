<?php

namespace Application\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\I18n\Translator\Translator;

class TranslatorPlugin extends AbstractPlugin
{
    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;
    function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    public function translate($string)
    {
        return $this->translator->translate($string);
    }
}