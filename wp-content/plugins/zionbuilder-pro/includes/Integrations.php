<?php

namespace ZionBuilderPro;

class Integrations {
	public function __construct() {
		add_action( 'zionbuilder/integrations/init', [ $this, 'init_integrations' ] );
	}

	public function init_integrations( $integrations_manager ) {
		$integrations_manager->register_integration( 'ZionBuilderPro\Integrations\WooCommerce\WooCommerce' );
		$integrations_manager->register_integration( 'ZionBuilderPro\Integrations\ACF\Acf' );
		$integrations_manager->register_integration( 'ZionBuilderPro\Integrations\Metabox\Metabox' );
	}
}
