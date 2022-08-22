<?php
/*
  Plugin Name: ClioWP Settings Page
  Plugin URI: https://github.com/pontikis/cliowp-settings-page
  Description: A test WordPress plugin to demonstrate Admin Settings Page creation
  Version: 1.0.0
  Author: Christos Pontikis
  Author URI: https://pontikis.net
  Text Domain: cliowp-settings-page
  Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ClioWP Settings Page plugin main class
 *
 * Before use this code in your own plugin:
 * - Change text domain (cliowp-settings-page) to your own text domain
 * - Make the appropriate changes in parameters in constructor
 */
class ClioWPSettingsPage
{
    /**
     * Settings Page title.
     *
     * @var string
     */
    private $page_title;

    /**
     * Menu title.
     *
     * @var string
     */
    private $menu_title;

    /**
     * Capability to access Settings page.
     *
     * @var string
     */
    private $capability;

    /**
     * Menu slug.
     *
     * @var string
     */
    private $menu_slug;

    /**
     * Settings form action.
     *
     * @var string
     */
    private $form_action;

    /**
     * Section A id.
     *
     * @var string
     */
    private $section1_id;

    /**
     * Section A title.
     *
     * @var string
     */
    private $section1_title;

    /**
     * Option group.
     *
     * @var string
     */
    private $option_group;

    /**
     * Input1 label.
     *
     * @var string
     */
    private $input1_label;

    /**
     * Input1 name.
     *
     * @var string
     */
    private $input1_name;

    /**
     * Constructor
     *
    */
    public function __construct()
    {
        // parameters
        $this->page_title = __('Test Settings Page', 'cliowp-settings-page');
        $this->menu_title = __('Test Settings', 'cliowp-settings-page');
        $this->capability = 'manage_options';
        $this->menu_slug  = 'cliowp-settings-page-slug';

        $this->form_action = 'options.php';

        $this->section1_id    = 'cliowp_settings_page_section1';
        $this->section1_title = __('Section A', 'cliowp-settings-page');

        $this->option_group = 'cliowp_sp_plugin';

        $this->input1_label = __('Input1 Label', 'cliowp-settings-page');
        $this->input1_name = 'cliowp_sp_input1';

        // actions
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'addSettings']);
        add_action('init', [$this, 'loadLanguages']);
    }

    /**
     * Adds a submenu page to the Settings main menu.
     */
    public function addSettingsPage()
    {
        /**
         * @param  string       $page_title The text to be displayed in the title tags of the page when the menu is selected.
         * @param  string       $menu_title The text to be used for the menu.
         * @param  string       $capability The capability required for this menu to be displayed to the user.
         * @param  string       $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
         * @param  callable     $callback   Optional. The function to be called to output the content for this page.
         * @param  int          $position   Optional. The position in the menu order this item should appear.
         * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
         */
        add_options_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            [$this, 'settingsPageHTML']
        );
    }

    public function addSettings()
    {
        /**
         * Adds a new section to a settings page.
         *
         * Part of the Settings API. Use this to define new settings sections for an admin page.
         * Show settings sections in your admin page callback function with do_settings_sections().
         * Add settings fields to your section with add_settings_field().
         *
         * The $callback argument should be the name of a function that echoes out any
         * content you want to show at the top of the settings section before the actual
         * fields. It can output nothing if you want.
         *
         * @param string   $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
         * @param string   $title    Formatted title of the section. Shown as the heading for the section.
         * @param callable $callback Function that echos out any content at the top of the section (between heading and fields).
         * @param string   $page     The slug-name of the settings page on which to show the section. Built-in pages include
         *                           'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
         *                           add_options_page();
         */
        add_settings_section(
            $this->section1_id,
            $this->section1_title,
            null,
            $this->menu_slug
        );

        /**
         * Adds a new field to a section of a settings page.
         *
         * Part of the Settings API. Use this to define a settings field that will show
         * as part of a settings section inside a settings page. The fields are shown using
         * do_settings_fields() in do_settings_sections().
         *
         * The $callback argument should be the name of a function that echoes out the
         * HTML input tags for this setting field. Use get_option() to retrieve existing
         * values to show.
         *
         * @param string   $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
         * @param string   $title    Formatted title of the field. Shown as the label for the field
         *                           during output.
         * @param callable $callback Function that fills the field with the desired form inputs. The
         *                           function should echo its output.
         * @param string   $page     The slug-name of the settings page on which to show the section
         *                           (general, reading, writing, ...).
         * @param string   $section  Optional. The slug-name of the section of the settings page
         *                           in which to show the box. Default 'default'.
         * @param array    $args     {
         *                           Optional. Extra arguments used when outputting the field.
         *
         *     @type string $label_for When supplied, the setting title will be wrapped
         *                             in a `<label>` element, its `for` attribute populated
         *                             with this value.
         *     @type string $class     CSS Class to be added to the `<tr>` element when the
         *                             field is output.
         * }
         */
        add_settings_field(
            'cliowp_sp_input1',
            $this->input1_label,
            [$this, 'input1HTML'],
            $this->menu_slug,
            $this->section1_id
        );


        /**
         * Registers a setting and its data.
         *
         * @param string $option_group A settings group name. Should correspond to an allowed option key name.
         *                             Default allowed option key names include 'general', 'discussion', 'media',
         *                             'reading', 'writing', and 'options'.
         * @param string $option_name The name of an option to sanitize and save.
         * @param array  $args {
         *     Data used to describe the setting when registered.
         *
         *     @type string     $type              The type of data associated with this setting.
         *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
         *     @type string     $description       A description of the data attached to this setting.
         *     @type callable   $sanitize_callback A callback function that sanitizes the option's value.
         *     @type bool|array $show_in_rest      Whether data associated with this setting should be included in the REST API.
         *                                         When registering complex settings, this argument may optionally be an
         *                                         array with a 'schema' key.
         *     @type mixed      $default           Default value when calling `get_option()`.
         * }
         */
        register_setting(
            $this->option_group,
            $this->input1_name,
            [
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'Test'
            ]
        );
    }

    function input1HTML() { ?>
        <input type="text" name="<?php echo $this->input1_name; ?>" value="<?php echo esc_attr(get_option($this->input1_name)) ?>">
      <?php }

    /**
     * Undocumented function
     */
    public function settingsPageHTML()
    { ?>

<div class="wrap">
    <h1><?php echo $this->page_title; ?>
    </h1>
    <form action="<?php echo $this->form_action; ?>" method="POST">
        <?php
        settings_fields($this->option_group);
        do_settings_sections($this->menu_slug);
        submit_button();
        ?>
    </form>
</div>

<?php }

    /**
     * Loads a plugin's translated strings.
     */
    public function loadLanguages()
    {
        /**
         * @param  string       $domain          Unique identifier for retrieving translated strings
         * @param  string|false $deprecated      Optional. Deprecated. Use the $plugin_rel_path parameter instead.
         *                                       Default false.
         * @param  string|false $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR where the .mo file resides.
         *                                       Default false.
         * @return bool         True when textdomain is successfully loaded, false otherwise.
         */
        load_plugin_textdomain(
            'cliowp-settings-page',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
}

// instantiate ClioWP Settings Page plugin main class
$clioWPSettingsPage = new ClioWPSettingsPage();
