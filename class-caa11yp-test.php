<?php
/**
 * Content Author Accessibility Preview
 *
 * @package content-author-accessibility-preview
 * @author Matt Rose
 * @license GPLv2
 * @since 1.2
 */

/**
 * Accessibility Test.
 *
 * @since 1.2
 */
class Caa11yp_Test implements \JsonSerializable {
	/**
	 * Identifier for Test.
	 *
	 * @var id string
	 */
	public $id;
	public $label;
	public $description;
	public $explanation;
	public $severity;
	public $args;

	public function __construct( $id, $label, $description, $explanation, $severity, $args ) {
		$this->id          = $id;
		$this->label       = $label;
		$this->description = $description;
		$this->explanation = $explanation;
		$this->severity    = $severity;
		$this->args        = $args;
	}

	public function jsonSerialize() {
		$return = get_object_vars( $this );
		if ( is_array( $this->args ) ) {
			foreach ( $this->args as $key => $value ) {
				$return[ $key ] = $value;
			}
			unset( $return['args'] );
		}
		return $return;
	}
}
