<?php
namespace ZionBuilderPro\Integrations\Metabox\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilderPro\Integrations\Metabox\Traits\Base;

class Text extends BaseField {
	use Base;

	public function get_category() {
		return [
			BaseField::CATEGORY_TEXT,
		];
	}

	public function get_id() {
		return 'meta-box-text';
	}
}
