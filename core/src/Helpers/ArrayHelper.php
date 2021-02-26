<?php


namespace App\Helpers;


class ArrayHelper
{
    /**
     * Excluir elementos de un array
     *
     * @param array $array
     * @param array $exceptKeys
     * @return array
     */
    public static function exceptItems(array $array, array $exceptKeys): array {
        return array_diff_key($array, array_flip($exceptKeys));
    }

    /**
     * Mostrar los items especficados de un array
     *
     * @param array $array
     * @param array $includeKeys
     * @return array
     */
    public static function onlyItems(array $array, array $includeKeys): array {
        $output = [];

        foreach ($array as $key => $value){
            if(in_array($key, $includeKeys)){
                $output[$key] = $value;
            }
        }

        return $output;
    }
}