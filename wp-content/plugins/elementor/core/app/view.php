<?php
namespace Elementor\Core\App;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var App $this
 */


$theme_class = 'dark' === $this->get_elementor_ui_theme_preference() ? 'eps-theme-dark' : '';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo __( 'Elementor', 'elementor' ) . ' ... '; ?></title>
		<base target="_parent">
		<?php wp_print_styles(); ?>
	</head>
	<body class="<?php echo $theme_class; ?>">
		<div id="e-app"></div>
		<?php wp_print_footer_scripts(); ?>
	</body>
</html>
