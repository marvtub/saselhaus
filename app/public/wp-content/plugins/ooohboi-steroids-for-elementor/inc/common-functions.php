<?php
/* Common task helpers */

if( ! defined( 'ABSPATH' ) ) exit;

define( 'SFE_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SFE_DIR_URL', plugin_dir_url( __FILE__ ) );

$options_page = array(
    'ob-landing-page'	=> array(
		'page_title'	=> __( 'Steroids for Elementor', 'ooohboi-steroids' ), 
        'icon_url'      => 'https://ooohboi.space/wp-content/plugins/ooohboi-steroids-for-elementor/assets/img/ooohboi-poopart-icon.png', 
        'subpages'		=> array(
            'ob-settings-page'	=> array(
                'page_title'	=> 'Settings', 
                'parent_slug'	=> 'ob_landing_page', 
                'sections'		=> array(
                    'section-ob-options' => array(
                        'title'			=> 'Settings', 
                        'text'			=> __( '<p>The following extensions are currently available with Steroids for Elementor add-on.<br/>Enable or disable particular extension by switching it ON or OFF.</p>', 'ooohboi-steroids' ), 
                        'fields'		=> array(
                            'ob_use_harakiri' => array(
                                'title'			=> 'HARAKIRI',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAllows you to change the writing mode of the Heading and Text Editor widgets%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ),
                                'checked'       => 1, 
                            ),
                            'ob_use_poopart' => array(
                                'title'			=> 'POOPART',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAdd an overlay or underlay ghost-element to any Elementor Widget%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_overlaiz' => array(
                                'title'			=> 'OVERLAIZ',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAn awesome set of options for the Background Overlay element manipulation%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_paginini' => array(
                                'title'			=> 'PAGININI',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sIt allows you to style up the posts pagination in Elementor%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_breakingbad' => array(
                                'title'			=> 'BREAKING BAD',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sA must to have extension for the Section and Columns%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_glider' => array(
                                'title'			=> 'GLIDER',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sThe content slider made out of Section and Columns (Swiper)%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_photogiraffe' => array(
                                'title'			=> 'PHOTOGIRAFFE',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sMake the Image widget full-height of the container%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_teleporter' => array(
                                'title'			=> 'TELEPORTER',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sThe Column hover controls for an exceptional effects%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_searchcop' => array(
                                'title'			=> 'SEARCH COP',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sDecide what to search for; posts only, pages only or everything%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_videomasq' => array(
                                'title'			=> 'VIDEOMASQ',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAdd the SVG mask to the Section video background and let the video play inside any shape%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_butterbutton' => array(
                                'title'			=> 'BUTTER BUTTON',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sDesign awesome buttons in Elementor%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_perspektive' => array(
                                'title'			=> 'PERSPEKTIVE',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sA small set of options that allow you to move widgets in 3D space%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_shadough' => array(
                                'title'			=> 'SHADOUGH',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sCreate the shadow that conforms the shape%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_photomorph' => array(
                                'title'			=> 'PHOTO MORPH',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAllows you to add the clip-path to the Image widget for Normal and Hover state%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_commentz' => array(
                                'title'			=> 'COMMENTZ',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAllows you to style up the post comments.', 'ooohboi-steroids%s' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_spacerat' => array(
                                'title'			=> 'SPACERAT',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAdds new shine to the Spacer widget.', 'ooohboi-steroids%s' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_imbox' => array(
                                'title'			=> 'IMBOX',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sImage Box widget extra controls', 'ooohboi-steroids%s' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_icobox' => array(
                                'title'			=> 'ICOBOX',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sIcon Box widget extra controls', 'ooohboi-steroids%s' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_hoveranimator' => array(
                                'title'			=> 'HOVER ANIMATOR',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAnimate widgets on columns mouse-over event', 'ooohboi-steroids%s' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_kontrolz' => array(
                                'title'			=> 'KONTROLZ',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sAllows you to additionaly style Image Carousel and Media Carousel controls%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_widget_stalker' => array(
                                'title'			=> 'WIDGET STALKER',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sStack widgets like flex elements%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                            'ob_use_pseudo' => array(
                                'title'			=> 'PSEUDO',
                                'type'			=> 'checkbox',
                                'text'			=> sprintf( __( '%sTake control over the Column\'s pseudo elements%s', 'ooohboi-steroids' ), '<span class="ob-option-desc">', '</span>' ), 
                                'checked'       => 1, 
                            ),
                        ),
                    ),
                ),
            ),
		), 
		'sections'		=> array(
			'section-one'	=> array(
				'title'			=> ' ',
				'text'			=> '',
				'include'		=> SFE_DIR_PATH . 'info/ob-landing.php',
			),
		),
	), 
);
$option_page = new RationalOptionPages( $options_page );