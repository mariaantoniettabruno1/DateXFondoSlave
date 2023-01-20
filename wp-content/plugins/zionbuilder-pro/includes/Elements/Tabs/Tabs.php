<?php

namespace ZionBuilderPro\Elements\Tabs;

use \ZionBuilder\Elements\Tabs\Tabs as FreeTabs;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class Tabs extends FreeTabs {
	/**
	 * Get label
	 *
	 * Sets the label that will appear in element list in edit mode
	 */
	public function is_wrapper() {
		return true;
	}
}
