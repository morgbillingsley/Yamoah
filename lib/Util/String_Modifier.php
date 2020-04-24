<?php

namespace Yamoah\Util;

/**
 * Class: String_Modifier
 */
class String_Modifier
{
    /**
     * @param string string to be formatted
     * @return string formatted title string
     */
    public static function title(string $string)
    {
        // Replace "_" character with space character
        $with_spaces = str_replace("_", " ", $string);
        // Replace "-" character with space character
        $with_spaces = str_replace("-", " ", $with_spaces);
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // Array of all the words that should remain lowercase
            $lowercase = array( "a", "an", "the", "for", "and", "nor", "but", "or", "yet", "so", "at", "around", "by", "after", "along", "for", "from", "of", "on", "to", "with", "without" );
            // If the word is the first in the sentence or if it is not found in the lowercase words array, then capitalize the word
            if ($i == 0 || ( !in_array($word, $lowercase)  )) {
                $words[$i] = ucfirst($word);
            }
        }
        // Glue the sentence back together and return it
        return implode(" ", $words);
    }

    /**
     * @param string string to be formatted
     * @param string the character to replace all delimeters with
     * @return string the newly formatted string
     */
    public static function replace_delimeter(string $string, string $replacement = " "): string
    {
        // An array of all the delimeters that should be replaced
        $delimeters = array( "_", "-", " ", ",", ".", "\t", "\n", "'" );
        $formatted = $string;
        // Loop through each delimeter, checking replacing every instance of it in the string
        foreach ($delimeters as $delimeter) {
            $formatted = str_replace( $delimeter, $replacement, $formatted );
        }
        // Return the newly formatted string
        return $formatted;
    }

    /**
     * @param string string to be formatted
     * @return string formatted snake case string
     */
    public static function snake(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $snake = self::replace_delimeter($lowercase, "_");

        return $snake;
    }

    /**
     * @param string string to be formatted
     * @return string formatted kebab case string
     */
    public static function kebab(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $kebab = self::replace_delimeter($lowercase, "-");

        return $kebab;
    }

    /**
     * @param string string to be formatted
     * @return string formatted camel case string
     */
    public static function camel(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $with_spaces = self::replace_delimeter( $lowercase );
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // If the word is the first in the sentence or if it is not found in the lowercase words array, then capitalize the word
            if ($i !== 0 ) {
                $words[$i] = ucfirst($word);
            }
        }
        // Glue the sentence back together and return it
        return implode("", $words);
    }

    /**
     * @param string string to be formatted
     * @return string formatted pascal case string
     */
    public static function pascal(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $with_spaces = self::replace_delimeter( $lowercase );
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // Capitalize the word
            $words[$i] = ucfirst($word);
        }
        // Glue the sentence back together and return it
        return implode("", $words);
    }

    /**
     * @param string string to remove
     * @param string string to remove from
     * @return string string with text removed
     */
    public static function remove(string $remove, string $host): string
    {
        return str_replace($remove, "", $host);
    }

    /**
     * @param string absolute path of file
     * @return string url of file to access from client
     */
    public static function path_to_url(string $path = ""): string
    {
        // Replace the absolute path of WordPress with the website url in the submitted path
        $url = str_replace(
            wp_normalize_path( untrailingslashit( ABSPATH ) ),
            site_url(),
            wp_normalize_path( $path )
        );
        // return the cleaned url
        return $url;
    }
}