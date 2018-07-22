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
trait Translatable
{
    /**
     * to get translation for attribute `$attr` on model call this method
     * by default fallback language is `en` if you want to override it
     * you can pass third parameter `$fall_back`. of course its cumbersome to define
     * fallback for each call so you can define `protected $fall_back_lang;` so this
     * function ignore third parameter. remember that model fall_back language has
     * higher priority than this argument.
     *
     * @param string $attr
     * @param string $lang
     * @param string $fall_back
     * @return string
     */
    public function getTranslationFor($attr, $lang, $fall_back = 'en')
    {
        echo 'call getTranslationFor';
        // boot function, check various conditions
        $this->translatableGetBoot($attr, $lang, $fall_back);
        if ($this->translationExist($attr, $lang)) {
            // simply return value from array :)
            return $this->{$attr}[$lang];
        } else if ($this->translationExist($attr, $fall_back)) {
            // return fall_back translation
            return $this->{$attr}[$fall_back];
        }
    }

    private function setFallBack(&$fall_back)
    {
        // if model defined fall_back property we use it, unless use function argument
        if (property_exists($this, "fall_back"))
            $fall_back = $this->fall_back;
    }

    private function translationExist($attr, $lang)
    {
        // determines if $this->{$attr} has key named $lang
        $keys = array_keys($this->{$attr});
        return in_array($lang, $keys);
    }

    private function translatableGetBoot($attr, $lang, &$fall_back)
    {
        echo 'call translatableGetBoot';
        // check data types
        $this->checkDataType($attr, $lang, $fall_back);
        if (!$this->checkPropExist($attr))
            throw \Exception('Attribute not found on model or not listed on $trans_attributes');
        $this->setFallBack($fall_back);
        echo "setFallBack {$fall_back}";
    }

    private function checkPropExist($attr)
    {
        $keys = array_keys($this->attributes);
        return in_array($attr, $keys) && in_array($attr, $this->trans_attributes);
    }

    private function checkDataType($attr, $lang, $fall_back)
    {
        echo 'call checkDataType';
        // check if $attr is string
        if (!is_string($attr))
            throw \Exception('$attr should be string');
        // check if $lang is string
        if (!is_string($lang))
            throw \Exception('$ang should be string');
        // check if $fall_back is string
        if (!is_string($fall_back))
            throw \Exception('$fall_back should be string');
        // check if $this->attr is array
        if (!is_array($this->{$attr}))
            throw \Exception("$attr should be array, maybe you forgot to add it to \$casts");
        echo 'checkDataType fine\n';
    }

    /**
     * this function get a attribute name, language code and its value and set translation for that attribute
     * attribute must be listed on `$trans_attributes`
     *
     * @param string $attr
     * @param string $lang
     * @param string $value
     */
    public function setTranslationFor($attr, $lang, $value)
    {
        $this->translatableSetBoot($attr, $lang);
        // if pass above step then [assign/update] [new/existing] translation
        $this->{$attr}[$lang] = $value;
    }

    private function translatableSetBoot($attr, $lang)
    {
        // check if $attr is string
        if (!is_string($attr))
            throw \Exception('$attr should be string');
        // check if $lang is string
        if (!is_string($lang))
            throw \Exception('$lang should be string');
        // check if $this->attr is array
        if (!is_array($this->{$attr}))
            throw \Exception("$attr should be array, maybe you forgot to add it to \$casts");
        $this->checkPropExist($attr);
    }
}
