<?php
/**
 * Posts.
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Admin;

use Phoenix\Sample_Structured_Export\Component\Users;

/**
 * Class Post
 */
class Options {





	/**
	 * Menu Slug.
	 *
	 * @var string
	 */
	public $menu_slug = 'sample-structured-export';

	/**
	 * Options prefix.
	 *
	 * @var string
	 */
	public $options_prefix = 'sample_structured_export_';

	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'notes_pages_scripts' ) );
	}

	/**
	 * Add css.
	 */
	public function notes_pages_scripts() {
		wp_register_style( 'notes-page', plugins_url( 'css/notes-page.css', __FILE__ ), array(), time() );
		wp_enqueue_style( 'notes-page' );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings".
		add_menu_page(
			'Settings Admin',
			'Sample Structured Export',
			'manage_options',
			$this->menu_slug,
			array( $this, 'sample_overview' ),
			'dashicons-carrot',
			3
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample PostTypes',
			'manage_options',
			$this->menu_slug . '-posttypes',
			array( $this, 'sample_post_types_notes' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample Taxonomies',
			'manage_options',
			$this->menu_slug . '-taxonomy',
			array( $this, 'sample_taxonomy_notes' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample PRs',
			'manage_options',
			$this->menu_slug . '-pull-requests',
			array( $this, 'sample_pull_requests_notes' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample Settings',
			'manage_options',
			$this->menu_slug . '-options',
			array( $this, 'sample_structured_export_settings' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample Priyank',
			'manage_options',
			$this->menu_slug . '-priyank',
			array( $this, 'sample_priyank_notes' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample Leo',
			'manage_options',
			$this->menu_slug . '-leo',
			array( $this, 'sample_leo_notes' )
		);

		add_submenu_page(
			$this->menu_slug,
			'Settings Admin',
			'Sample Paul',
			'manage_options',
			$this->menu_slug . '-paul',
			array( $this, 'sample_paul_notes' )
		);
	}

	/**
	 * Register and add settings
	 */
	public function sample_overview() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		$screen = get_current_screen();
		echo Admin_Page_Additions::show_some_info();
		echo '<div class="add-to-sample-dash">';
		echo "<h4> ** <span style=\"color:salmon;\">$screen->id</span> == Screen->id</h4>";
		do_action( 'add_to_sample_overview' );
		echo '
<pre>
define( \'WP_DEBUG\', true );
define( \'WP_DEBUG_LOG\', true );
define( \'WP_DEBUG_DISPLAY\', true );
</pre>
		';
		echo '</div>';

		echo '<h4 style="color:rgba(250,128,114,.7);">Current Screen is <span style="color:rgba(250,128,114,1);">' . $screen->id . '</span></h4>';

		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_priyank_notes() {
		echo '<div class="wrap priyank">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Priyank_Admin_Notes::notes_hook();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_leo_notes() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Leo_Admin_Notes::notes_hook();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_taxonomy_notes() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Taxonomy_Admin_Notes::notes_hook();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_pull_requests_notes() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Pull_Request_Reviews::notes_hook();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_paul_notes() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Paul_Admin_Notes::notes_hook();
		echo '</div>';
	}

	/**
	 * Register and add settings
	 */
	public function sample_post_types_notes() {
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		Post_Type_Admin_Notes::notes_hook();
		echo '</div>';
	}
	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting( 'sample-udf-options', $this->options_prefix . 'body_parser_url' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'migration_rx_url' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'migration_env' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'no_content_router' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'migration_api_key' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'image_alias_search' );
		register_setting( 'sample-udf-options', $this->options_prefix . 'image_alias_replace' );
	}

	/**
	 * Options page callback
	 */
	public function sample_structured_export_settings() {
		?>
		<div class="wrap">
			<h1>Sample Structured Export Settings</h1>
			<form method="post" action="options.php">
			<?php settings_fields( 'sample-udf-options' ); ?>
			<?php do_settings_sections( 'sample-udf-options' ); ?>
				<table class="ti-settings-section" style="width: 100%;">
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Body Parser URL:
						</td>
						<td>
							<input type="text" class="input-text" style="width: 50%;" name="<?php echo esc_attr( $this->options_prefix ); ?>body_parser_url" value="<?php echo esc_url( get_option( $this->options_prefix . 'body_parser_url' ) ); ?>" /><br />
							<small><em>URL to the body parser.</em></small>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Hyper Migration URL:
						</td>
						<td>
							<input type="text" class="input-text" style="width: 50%;" name="<?php echo esc_attr( $this->options_prefix ); ?>migration_rx_url" value="<?php echo esc_url( get_option( $this->options_prefix . 'migration_rx_url' ) ); ?>" /><br />
							<small><em>URL for hyper migrations.</em></small>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Hyper Migration ENV:
						</td>
						<td>
							<select name="<?php echo esc_attr( $this->options_prefix ); ?>migration_env" value="<?php echo esc_attr( get_option( $this->options_prefix . 'migration_env' ) ); ?>">
								<option value='hyper-dev' <?php selected( get_option( $this->options_prefix . 'migration_env' ), 'hyper-dev' ); ?>>hyper-dev</option>
								<option value='hyper-test'<?php selected( get_option( $this->options_prefix . 'migration_env' ), 'hyper-test' ); ?>> hyper-test</option>
								<option value='hyper-prod'<?php selected( get_option( $this->options_prefix . 'migration_env' ), 'hyper-prod' ); ?>> hyper-prod</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Do NOT Send to Content Router:
						</td>
						<td>
						<?php $check_value = get_option( $this->options_prefix . 'no_content_router' ); ?>
							<input type="checkbox" name="<?php echo esc_attr( $this->options_prefix ); ?>no_content_router" value="1" <?php echo checked( '1', $check_value, false ); ?>>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Hyper Migration API Key:
						</td>
						<td>
							<input type="text" class="input-text" style="width: 50%;" name="<?php echo esc_attr( $this->options_prefix ); ?>migration_api_key" value="<?php echo esc_attr( get_option( $this->options_prefix . 'migration_api_key' ) ); ?>" /><br />
							<small><em>API Key for hyper migrations.</em></small>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; width: 150px; text-align: right; padding-top: 6px;">
							Hyper Migration Image Alias:
						</td>
						<td>
							<input type="text" class="input-text" style="width: 50%;" name="<?php echo esc_attr( $this->options_prefix ); ?>image_alias_search" value="<?php echo esc_attr( get_option( $this->options_prefix . 'image_alias_search' ) ); ?>" /><br />
							<small><em>Search for this text.</em></small>
						</td>
						<td>
							<input type="text" class="input-text" style="width: 50%;" name="<?php echo esc_attr( $this->options_prefix ); ?>image_alias_replace" value="<?php echo esc_attr( get_option( $this->options_prefix . 'image_alias_replace' ) ); ?>" /><br />
							<small><em>Replace with this text. </em></small>
						</td>
					</tr>
				</table>
			<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
