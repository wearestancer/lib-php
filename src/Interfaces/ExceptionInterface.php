<?php

namespace ild78\Interfaces;

/**
 * Regrouping every exceptions
 */
interface ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string;
}
