<?php

namespace srag\Plugins\UserDefaults\Form;

use ilGlyphGUI;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class udfGlyphGUI extends ilGlyphGUI {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;

	/**
	 * Get glyph html
	 *
	 * @param string $a_glyph glyph constant
	 * @param string $a_text  text representation
	 *
	 * @return string html
	 */
	static function get(string $a_glyph, string $a_text = ""): string
    {
		if ($a_glyph == 'remove') {
			self::$map[$a_glyph]['class'] = 'glyphicon glyphicon-' . $a_glyph;
		}
		if (!isset(self::$map[$a_glyph])) {
			self::$map[$a_glyph]['class'] = 'glyphicon glyphicon-' . $a_glyph;
		}

		return parent::get($a_glyph, $a_text) . ' ';
	}


	static function gets(string $a_glyph): string
    {
		self::$map[$a_glyph]['class'] = 'glyphicons glyphicons-' . $a_glyph;

		return parent::get($a_glyph, '') . ' ';
	}
}