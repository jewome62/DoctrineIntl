<?php

namespace DavidDel\DoctrineIntlBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait Translatable
{
    /**
     * @var Collection
     */
    protected $translations;

    /**
     * @var TranslationInterface
     */
    protected $translation;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param TranslationInterface $translation
     * @return TranslatableInterface
     */
    public function addTranslation(TranslationInterface $translation)
    {
        $this->translations[$translation->getLocale()] = $translation;
        $translation->setTranslatable($this);

        return $this;
    }

    /**
     * @param TranslationInterface $translation
     * @return TranslatableInterface
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        $this->translations->removeElement($translation);
        $translation->setTranslatable(null);

        return $this;
    }

    /**
     * @param string|null $locale
     * @return TranslationInterface
     */
    public function getTranslation($locale = null)
    {
        return $this->translation ?: $this->translation = $this->getTranslationProxy($locale)->getTranslation();
    }

    /**
     * @param string|null $locale
     * @return TranslationProxyInterface
     */
    public function getTranslationProxy($locale = null)
    {
        return new TranslationProxy($this, $locale);
    }

    public function __call($method, $arguments)
    {
        $locale = null;
        if (count($arguments) > 1) {
            $locale = array_pop($arguments);
        }

        return call_user_func_array(array($this->getTranslationProxy($locale), $method), $arguments);
    }

    public function __get($property)
    {
        if (property_exists($this->getTranslation(), $property)) {
            $getter = 'get'.ucfirst($property);
            return $this->getTranslation()->$getter();
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this->getTranslation(), $property)) {
            $setter = 'set'.ucfirst($property);
            return $this->getTranslation()->$setter($value);
        }
    }
} 