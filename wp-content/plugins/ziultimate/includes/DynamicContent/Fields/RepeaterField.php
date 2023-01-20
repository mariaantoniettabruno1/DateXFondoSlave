<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\Fields\RepeaterField as ZionProRepeaterField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RepeaterField
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class RepeaterField extends ZionProRepeaterField {
	public function get_category() {
		return [ self::CATEGORY_TEXT, self::CATEGORY_IMAGE, self::CATEGORY_LINK];
	}
}