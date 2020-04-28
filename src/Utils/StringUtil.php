<?php

namespace App\Utils;

use App\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class StringUtil
 *
 * @package App\Utils
 */
Class StringUtil
{
    /**
     * This function convert text from snake case format to camel case
     *
     * @param string $text
     * @return string
     */
    public function convertSnakeToCamel(string $text): string
    {
        $tabText = explode('_', $text);
        $output = "";

        foreach ($tabText as $t) {
            $output .= ucwords($t);
        }

        return $output;
    }
}