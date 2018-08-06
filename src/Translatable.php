<?php

namespace Mortza\Translatable;

/**
 * Trait Translatable
 *
 * with this package you can add translation to laravel models
 * to setup translation you should add this trait to your Model.php file
 * then define parameters listed below in class
 * with this package there is no limitation on number of translation for each attribute except database limitations
 *
 * to enable translation for a model you should add attribute names you want to translate to the
 * `protected $trans_attributes` array. attributes tagged for translation should be json column on database.
 *
 * example:
 *
 * ```php
 * protected $trans_attributes = ['description','name',];
 * ```
 *
 * plus you should add these values to `protected $casts` to cast to array
 *
 * example:
 *
 * ```php
 * protected $casts = ['description'=>'array','name'=>'array',];
 * ```
 * @package Mortza\Translatable
 *
 */
trait Translatable {
    /**
     * to get translation for attribute `$attr` on model call this method
     * by default fallback language is `en` if you want to override it
     * you can pass third parameter `$fall_back`. of course its cumbersome to define
     * fallback for each call so you can define `protected $fall_back_lang;` so this
     * function ignore third parameter. remember that model fall_back_lang language has
     * lower priority than this argument.
     *
     * @param string $attr
     * @param string $lang
     * @param string $fall_back
     * @return string
     */
    public function getTranslationFor(string $attr, string $lang, string $fall_back = null) {
        // boot function, check various conditions
        $fall_back = is_null($fall_back) ? $this->fall_back_lang : $fall_back;
        // if property is not translatable return it normally
        if (!$this->isTranslatable($attr)) {
            return parent::getAttributeValue($attr);
        } else if ($this->translationExist($attr, $lang)) {
            return parent::getAttributeValue($attr)[$lang];
        } else if ($this->translationExist($attr, $fall_back)) {
            // return fall_back translation
            return parent::getAttributeValue($attr)[$fall_back];
        }
    }

    private function translationExist($attr, $lang) {
        // determines if $this->{$attr} has key named $lang
        $keys = array_keys(parent::getAttribute($attr));
        return in_array($lang, $keys);
    }

    private function translatableBoot($attr) {
        if (!$this->isTranslatable($attr)) {
            throw \Exception("$attr is not a translatable property. consider add it to \$trans_attributes");
        }

    }

    /**
     * this function get a attribute name, language code and its value and set translation for that attribute
     * attribute must be listed on `$trans_attributes`
     *
     * @param string $attr
     * @param string $lang
     * @param string $value
     */
    public function setTranslationFor(string $attr, string $lang, $value) {
        if (!$this->isTranslatable($attr)) {
            parent::setAttribute($attr, $value);
        } else {
            // if pass above step then [assign/update] [new/existing] translation
            $temp = parent::getAttribute($attr);
            $temp[$lang] = $value;
            parent::setAttribute($attr, $temp);
        }
    }

    /**
     * determine if a attribute is translatable or not
     *
     * @param string $attr
     * @return bool
     */
    public function isTranslatable(string $attr) {
        if (is_null($this->trans_attributes)) {
            return false;
        } else {
            return in_array($attr, $this->trans_attributes);
        }

    }

    /**
     * return a array contains all available translation keys (language codes)
     *
     * @param string $attr
     * @return array
     */
    public function availableTranslationsFor(string $attr) {
        if ($this->isTranslatable($attr)) {
            $keys = array_keys(parent::getAttributeValue($attr));
            return $keys;
        } else {
            throw \Exception("$attr is not translatable.");
        }

    }

    /**
     * alias for $this->availableTranslationsFor
     *
     * @param string $attr
     * @return array
     */
    public function translations(string $attr) {
        return $this->availableTranslationsFor($attr);
    }

    /**
     * if third parameter not passed to function its alias for return $this->getTranslationFor($attr, $lang);
     * unless its alias for return $this->setTranslationFor($attr, $lang, $value);
     *
     * @param string $attr
     * @param string $lang
     * @param $value
     * @return string
     */
    public function translate(string $attr, string $lang, $value = null) {
        if (is_null($value)) {
            return $this->getTranslationFor($attr, $lang);
        } else {
            $this->setTranslationFor($attr, $lang, $value);
        }

    }

    /**
     * remove translation for specific $lang on $attr
     *
     * @param string $attr
     * @param string $lang
     */
    public function removeTranslationFor(string $attr, string $lang) {
        $this->translatableBoot($attr);

        if ($this->translationExist($attr, $lang)) {
            $temp = parent::getAttribute($attr);
            unset($temp[$lang]);
            parent::setAttribute($attr, $temp);
        }
    }

    /**
     * removes all translations for $lang in $this->trans_attributes
     * @param string $lang
     */
    public function removeAllTranslations(string $lang) {
        foreach ($this->trans_attributes as $key => $value) {
            $this->removeTranslationFor($value, $lang);
        }

    }
}
