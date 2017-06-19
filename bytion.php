<?php
/**
 * Plugin Name:       Bytion Coding Challenge
 * Plugin URI:        http://bytion.io/
 * Description:       Create a WordPress Plugin
 * Version:           3.0.0
 * Author:            Sean Warren
 * Author URI:        http://github.com/smwarren
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       SWBCC
 *
 * @link              http://bytion.io/
 * @package           SWBCC
 */

// If this file is called directly, abort.
if (!defined('WPINC'))
{
    die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if (!defined('SWBCC_VERSION'))
{
    define('SWBCC_VERSION', '3.0.0');
}

if (!defined('SWBCC_NAME'))
{
    define('SWBCC_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
}

if (!defined('SWBCC_DIR'))
{
    define('SWBCC_DIR', WP_PLUGIN_DIR . '/' . SWBCC_NAME);
}

if (!defined('SWBCC_URL'))
{
    define('SWBCC_URL', WP_PLUGIN_URL . '/' . SWBCC_NAME);
}


class SWBCC
{
    public function __construct()
    {

        /**
         * BCC Form Functionality
         *
         * @since 1.0.0
         */
        if (file_exists(SWBCC_DIR . '/shortcode/BCC_Form.php'))
        {
            require_once(SWBCC_DIR . '/shortcode/BCC_Form.php');
        }

        $this->init();

    }

    public function init()
    {
        add_action('init', array(
            $this,
            'SWBCC_CREATE_DB'
        ));
        add_action('init', array(
            $this,
            'SWBCC_BOOK_CATEGORY_TAXONOMIES'
        ));
        add_action('init', array(
            $this,
            'SWBCC_Custom_BOOK_POST_TYPE'
        ));
        add_action('init', array(
            $this,
            'SWBCC_REGISTER_ADD_TERMS'
        ));
        add_action('init', array(
            $this,
            'SWBCC_PLUGIN_MENU'
        ));

        wp_enqueue_style('SWBCC_STYLES', SWBCC_URL . '/SWBCC.css');


    }

    /**
     * Creates The Menu Items
     *
     * Administration pages where users can view
     * submitted data from the new table.
     * @since    3.0.0
     *
     * @return Adds the a plugin menu at the top of the settings menu
     **/

    function SWBCC_PLUGIN_MENU()
    {
        add_options_page('SWBCC Plugin Options', 'SWBCC Plugin', 'manage_options', 'SWBCC_ADMIN', array(
            $this,
            'SWBCC_PLUGIN_OPTIONS'
        ));
    }

    /**
     * Creates an Admin Page
     *
     * Administration pages where users can view
     * submitted data from the new table.
     * @since    3.0.0
     *
     * @return An Admin page thats displays the data submitted data
     **/


    function SWBCC_PLUGIN_OPTIONS()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'SWBCC';

        if (isset($_GET['SWBCC_DEL']))
        {
            $wpdb->delete($table_name, array(
                'ID' => $_GET['SWBCC_DEL']
            ));
        }
        $bookObject = $wpdb->get_results("SELECT * FROM $table_name");

        if ($bookObject)
        {
            echo '<div class="SWBCC_TABLE"><center><h2>The Submitted Data</h2><hr><table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($bookObject as $data)
            {

                echo '<tr>
                <td>' . $data->name . '</td>
                <td>' . $data->email . '</td>
                <td><a href="?page=SWBCC_ADMIN&SWBCC_DEL=' . $data->id . '"> Delete</a></td>
              </tr>';

            }
        }
        echo '  </tbody>
      </table><br><h2>Add A New Book</h2><hr>' . do_shortcode('[BCCFORM]') . ' </center></div>';
    }

    /**
     * Creates a new table in the Wordpress Datatbase
     *
     * -v2 A new table added to the database with a nameand email field
     * -v3 Modify the plugin database table to include a field for date/time submitted
     * @since    3.0.0
     *
     * @return a new table in the database
     **/
    function SWBCC_CREATE_DB()
    {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . 'SWBCC';

        $sql = "CREATE TABLE $table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		name text NOT NULL,
    		email text NOT NULL,
        date TIMESTAMP NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    		UNIQUE KEY id (id)
    	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Registers the Custom Book Post Type
     *
     * A custom post type that only WordPress Admin users can manage.
     * Please name this custom posttype BOOKS.
     * @since    1.0.0
     *
     * @return a custom post type called Book
     **/

    function SWBCC_Custom_BOOK_POST_TYPE()
    {

        $labels = array(
            'name' => 'Books',
            'singular_name' => 'Book',
            'menu_name' => 'Book Post Types',
            'name_admin_bar' => 'Book Post Type',
            'archives' => 'Book Archives',
            'attributes' => 'Book Attributes',
            'parent_item_colon' => 'Parent Book:',
            'all_items' => 'All Books',
            'add_new_item' => 'Add New Book',
            'add_new' => 'Add New Book',
            'new_item' => 'New Book',
            'edit_item' => 'Edit Book',
            'update_item' => 'Update Book',
            'view_item' => 'View Book',
            'view_items' => 'View Books',
            'search_items' => 'Search Book',
            'not_found' => 'Book Not found',
            'not_found_in_trash' => 'Book Not found in Trash',
            'featured_image' => 'Book Featured Image',
            'set_featured_image' => 'Set Book featured image',
            'remove_featured_image' => 'Remove Book featured image',
            'use_featured_image' => 'Use as Book featured image',
            'insert_into_item' => 'Insert into Book',
            'uploaded_to_this_item' => 'Uploaded to this Book',
            'items_list' => 'Books list',
            'items_list_navigation' => 'Books list navigation',
            'filter_items_list' => 'Filter Books list'
        );
        $args   = array(
            'label' => 'Book',
            'description' => 'Post Type Description',
            'labels' => $labels,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'trackbacks',
                'revisions',
                'custom-fields',
                'page-attributes',
                'post-formats'
            ),
            'taxonomies' => array(
                'BOOK_CATEGORY'
            ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page'
        );
        register_post_type('SWBCC_BOOK', $args);

    }

    /**
     * Registers the Book Category Taxonomies
     *
     * A new Taxonomy to use with the custom post type, again only Admin users
     *  should be able to manage. Please name your Taxonomy, BOOK CATEGORY.
     *
     * @since    1.0.0
     * @return a new Taxonomy Called Book Category
     **/

    function SWBCC_BOOK_CATEGORY_TAXONOMIES()
    {
        register_taxonomy('BOOK_CATEGORY', 'SWBCC_BOOK', array(
            'labels' => array(
                'name' => 'Book Category',
                'add_new_item' => 'Add New BOOK Category',
                'new_item_name' => "New Movie BOOK Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        ));
    }

    /**
     * Registers default terms
     *
     *– A new Taxonomy to use with the custom post type,again only Admin users
     *  should be able to manage. Please name your Taxonomy,
     * ------ BOOK CATEGORY with a default term NON FICTION.------
     *
     * @since    1.0.0
     * @return adds the default term for the custom post type book
     **/


    function SWBCC_REGISTER_ADD_TERMS()
    {
        $this->taxonomy = 'BOOK_CATEGORY';
        $this->terms    = array(
            '0' => array(
                'name' => 'Non Fiction',
                'slug' => 'non-fiction',
                'description' => 'A Non-Fiction Book'
            )
        );

        foreach ($this->terms as $term_key => $term)
        {
            wp_insert_term($term['name'], $this->taxonomy, array(
                'description' => $term['description'],
                'slug' => $term['slug']
            ));
            unset($term);
        }

    }

}

$initPlugin = new SWBCC();
