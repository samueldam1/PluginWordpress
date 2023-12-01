<?php
/**
 * @package Words
 * @version 0.0.1
 */
/*
Plugin Name: Words
Plugin URI: http://wordpress.org/plugins/words/
Description: 
Author: 
Version: 0.0.1
Author URI: 
*/


# list of offensive words
$offensiveWordsList = [
    "coño",
    "joder"
    "puta"
];
$nonOffensiveWordsList = [
    "caca",
    " culo",
    "pedo",
    "pis"
];

/**
 * Whenever the word WordPress appears in the content
 * of a post or a page,
 * it will be replaced
 * This time using the database
 * @param $text string
 * @return string
 */
function wordpress_typo_fix($text){
    // take the words from the table
    $words = selectData();
    foreach ($words as $result){
        $offensiveWords[] = $result->offensiveWords; // -> para seleccionar que columna escoger
        $nonOffensiveWords[] = $result->nonOffensiveWord;
    }
    return str_replace($offensiveWords, $nonOffensiveWords, $text);
}

add_filter('the_content', 'renym_wordpress_typo_fix');

/**
 * To do this but with databases,
 * First, we will create the table
 */
function createTable(){
    global $wpdb; // this is how you get access to the database
    $table_name = $wpdb->prefix . 'Words';

    $charset_collate = $wpdb->get_charset_collate();
    // SQL sentence
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        offesinveWord varchar(255) NOT NULL,
        nonOffensiveWord varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    // including the file to use dbDelta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    // executing the SQL sentence
    dbDelta( $sql );
}
// when the plugin is activated, we create the table
add_action( 'plugins_loaded', 'createTable' );

// now we insert the words into the table
function insertData(){
    global $wpdb, $offensiveWordsList, $nonOffensiveWordsList;
    $table_name = $wpdb->prefix . 'Words';
    // we see if the table is empty
    $hasSomething = $wpdb->get_results( "SELECT * FROM $table_name" );
    if ( count($hasSomething) == 0 ) {
        // if it is empty, we insert the words
        for ($i = 0; $i < count($offensiveWordsList); $i++) {
            $wpdb->insert(
                $table_name,
                array(
                    'offesinveWord' => $offensiveWordsList[$i],
                    'nonOffensiveWord' => $nonOffensiveWordsList[$i]
                )
            );
        }
    }
}

// when the plugin is activated, we insert the words
add_action( 'plugins_loaded', 'insertData' );

// selecting the words from the database
function selectData(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'Words';
    $results = $wpdb->get_results( "SELECT * FROM $table_name" );
    return $results;
}