<?php

class Easy_Json {
    /**
      * Indents a flat JSON string to make it more human-readable.
      * @param string $json The original JSON string to process.
      * @return string Indented version of the original JSON string.
      */
    const STR_NEW_LINE  = "\n";
    const STR_INDENT    = "    ";

    /* {{{ public static function format ($json)  */
    public static function format ($json) {

        $strRet = "";
        $intIndent = 0;
        $bolOutOfQuote = true;

        $charCurrent    = "";
        $charPrevious   = "";

        preg_match_all("/([^\\\\\"{}\\[\\]:,]+|[\\\\\"{}\\[\\]:,])/", $json, $arrMatch);
        foreach ($arrMatch[0] as $charCurrent) {

            switch ($charCurrent) {
                case "\"" :
                    if ($bolOutOfQuote || $charPrevious != "\\") {
                        $bolOutOfQuote = !$bolOutOfQuote;
                    }
                    $strRet .= $charCurrent;
                    break;
                case "}":
                case "]":
                    if ($bolOutOfQuote) {
                        $intIndent --;
                        $strRet .= self::STR_NEW_LINE;
                        $strRet .= str_repeat(self::STR_INDENT, $intIndent);
                    }
                    $strRet .= $charCurrent;
                    break;
                case ":":
                    $strRet .= $bolOutOfQuote ? " {$charCurrent} " : $charCurrent;
                    break;
                case "{":
                case "[":
                    if ($bolOutOfQuote) {
                        $intIndent ++;
                    }
                case ",":
                    $strRet .= $charCurrent;
                    if ($bolOutOfQuote) {
                        $strRet .= self::STR_NEW_LINE;
                        $strRet .= str_repeat(self::STR_INDENT, $intIndent);
                    }
                    break;
                default:
                    $strRet .= $charCurrent;
            }
            $charPrevious = $charCurrent;
        }

        return $strRet;

    } 
    /* }}} */

}
