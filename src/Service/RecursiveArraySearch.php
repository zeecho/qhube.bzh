<?php

namespace App\Service;

// source: https://gist.github.com/unique1984/6472eacfbffaf6e8332d69c0d83c0d68
class RecursiveArraySearch {
    private $arraySearchResult = array();

    //~ start of recursive array_
    //~ alias of special array_search
    public function my_array_search($needle, $haystack)
    {
        return $this->my_recursive_array_search($needle, $haystack, false, false);
    }

    //~ alias of special array_key_exists
    private function my_recursive_array_search($needle, $haystack, $in = false, $recursive = false, $before = null, $originalData = array())
    {
        /* using:
         * if [$in = false and $recursive = false] 1 dimention array keys of needle==value, result can be multiple.
         * if [$in = true and $recursive = false] it is the same as array_key_exists() but this can return value of that key
         * if [$in = false and $recursive = true] it searchs all of values and returns  || $array[][key,that,point,of]=$value_needle || formatted comma seperated
         * if [$in = true and $recursive = true] it searchs all of keys and returns || $array[key][points][key][needle]=$value; || formatted comma seperated
         * $before and $originalData[f] recursive args do not bother it :)
        */
        //~ future use, some kind of check mechanism or rarrayKeyToData() will integrate in this function
        if (count($originalData) == 0) {
            $originalData = $haystack;
        }

        if (!$in && !$recursive) {
            //~
            $returnData = array();
            foreach ($haystack as $key => $value) {
                if (preg_match('/' . $needle . '/ui', $value)) {
                    $returnData[] = $key;
                }
            }
            return count($returnData) > 0 ? array_values($returnData) : false;
            //~
        } elseif ($in && !$recursive) {
            //~
            return array_key_exists($needle, $haystack) ? $haystack[$needle] : false;
            //~
        } elseif (!$in && $recursive) {
            //~
            foreach ($haystack as $key => $value) {
                if (is_array($value)) {
                    $this->my_recursive_array_search($needle, $value, false, true, $before . $key . ".");
                } elseif (preg_match('/' . $needle . '/ui', $value)) {
                    $this->arraySearchResult[$before . $key] = $value;
                }
            }
            if (count($this->arraySearchResult) > 0) {
                return $this->arraySearchResult;
            } else {
                return false;
            }
            //~
        } elseif ($in && $recursive) {
            //~
            foreach ($haystack as $key => $value) {
                if (is_array($value)) {
                    if (preg_match('/' . $needle . '/ui', $key)) {
                        $this->arraySearchResult[$before . $key] = $value;
                    }
                    $this->my_recursive_array_search($needle, $value, true, true, $before . $key . ",");
                } elseif (preg_match('/' . $needle . '/ui', $key)) {
                    $this->arraySearchResult[$before . $key] = $value;
                }
            }
            if (count($this->arraySearchResult) > 0) {
                return $this->arraySearchResult;
            } else {
                return false;
            }
            //~
        }
    }

    //~ alias of recursive array_search

    public function my_array_key_exists($needle, $haystack)
    {
        return $this->my_recursive_array_search($needle, $haystack, true, false);
    }

    //~ alias of recursive array_key_exists

    public function rarray_search($needle, $haystack)
    {
        $this->arraySearchResult = array();
        return $this->my_recursive_array_search($needle, $haystack, false, true);
    }

    public function rarray_key_exists($needle, $haystack)
    {
        $this->arraySearchResult = array();
        return $this->my_recursive_array_search($needle, $haystack, true, true);
    }

    public function rarrayKeyToData($explodable, $haystack, $arr = false)
    {
        $value = $haystack;
        //~ foreach(explode(",",$explodable) as $k){
        //~ echo $arr?$k."\n":null;
        //~ $value=$value[$k];
        //~ }
        return $value[explode(",", $explodable)[0]];
    }
}