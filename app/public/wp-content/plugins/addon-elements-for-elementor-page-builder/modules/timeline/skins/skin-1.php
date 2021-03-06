<?php

namespace WTS_EAE\Modules\Timeline\Skins;

use Elementor\Widget_Base;


class Skin_1 extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions(); // TODO: Change the autogenerated stub
		add_action( 'elementor/element/eae-timeline/skin1_icon_global_style/after_section_end', [ $this, 'extra_control' ] );
	}

	public function get_id() {
		return 'skin1';
	}

	public function get_title() {
		return __( 'Skin 1', 'wts-eae' );
	}
	public function register_items_control( Widget_Base $widget ) {
		$this->parent = $widget;
	}
	public function register_style_controls() {
		parent::register_style_controls(); // TODO: Change the autogenerated stub
		$this->eae_timeline_style_section();
	}

	public function extra_control() {
		$this->update_control(
			'item_icon_icon_size',
			[
				'default' => [
					'size' => 25,
				],
			]
		);

		$this->update_control(
			'item_icon_icon_padding',
			[
				'default' => [
					'size' => 7,
				],
			]
		);
		$this->update_control(
			'arrow_align',
			[
				'default' => 'center',
			]
		);
	}

	public function render() {
		$this->common_render();
	}
}
