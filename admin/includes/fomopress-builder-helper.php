<?php

function fomopress_builder_args() {
    return array(
        'id'           => 'fomopress_metabox_wrapper',
        'title'        => __('FomoPress', 'fomopress'),
        'object_types' => array( 'fomopress' ),
        'context'      => 'normal',
        'priority'     => 'high',
        'show_header'  => false,
        'tabnumber'    => true,
        'layout'       => 'horizontal',
        'tabs'         => apply_filters( 'fomopress_builder_tabs', array(
            'source_tab' => array(
                'title'         => __('Source', 'fomopress'),
                'icon'          => 'database.svg',
                'sections'      => array(
                    'config'        => array(
                        'title'             => __('Select Source', 'fomopress'),
                        'fields'            => array(
                            'display_type'  => array(
                                'type'      => 'select',
                                'label'     => __('I would like to display' , 'fomopress'),
                                'default'   => 'press_bar',
                                'options'   => FomoPress_Helper::notification_types(),
                                'toggle'   => [
                                    'comments'    => FomoPress_Helper::comments_toggle_data(),
                                    'press_bar'   => FomoPress_Helper::press_bar_toggle_data(),
                                    'conversions' => FomoPress_Helper::conversions_toggle_data(),
                                ],
                                'hide' => FomoPress_Helper::hide_data( 'display_types' ),
                                'priority' => 50
                            ),
                            'conversion_from'  => array(
                                'type'     => 'select',
                                'label'    => __('From' , 'fomopress'),
                                'default'  => 'custom',
                                'options'  => FomoPress_Helper::conversion_from(),
                                'priority' => 60,
                                'toggle'   => FomoPress_Helper::conversion_toggle(),
                            ),
                            'press_content' => array(
                                'type'     => 'editor',
                                'label'    => __('Content' , 'fomopress'),
                                'priority' => 70,
                            ),
                        ),
                    ),
                )
            ),
            'design_tab' => array(
                'title'      => __('Design', 'fomopress'),
                'icon'       => 'screen.svg',
                'sections'   => array(
                    'themes' => array(
                        'title'      => __('Themes', 'fomopress'),
                        'priority' => 5,
                        'fields'   => array(
                            'theme' => array(
                                'type'      => 'theme',
                                'priority'	=> 5,
                                'default'	=> 'theme-one',
                                'options'   => FomoPress_Helper::colored_themes(),
                            ),
                        )
                    ),
                )
            ),
            'display_tab' => array(
                'title'         => __('Display', 'fomopress'),
                'icon'          => 'screen.svg',
                'sections'      => array(
                    'appearance'        => array(
                        'title'    => __('Appearance', 'fomopress'),
                        'priority' => 10,
                        'fields'   => array(
                            'pressbar_position'  => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'fomopress'),
                                'priority'	=> 40,
                                'options'   => [
                                    'top'       => __('Top' , 'fomopress'),
                                    'bottom'    => __('Bottom' , 'fomopress'),
                                ],
                            ),
                            'conversion_position'  => array(
                                'type'      => 'select',
                                'label'     => __('Position' , 'fomopress'),
                                'priority'	=> 50,
                                'options'   => [
                                    'bottom_left'       => __('Bottom Left' , 'fomopress'),
                                    'bottom_right'      => __('Bottom Right' , 'fomopress'),
                                ],
                            ),
                        ),
                    ),
                    'visibility'        => array(
                        'title'    => __('Visibility', 'fomopress'),
                        'priority' => 1000,
                        'fields'   => array(
                            'show_on'  => array(
                                'type'      => 'select',
                                'label'     => __('Show On' , 'fomopress'),
                                'priority'	=> 10,
                                'options'   => [
                                    'everywhere'       => __('Show Everywhere' , 'fomopress'),
                                    'on_selected'      => __('Show On Selected' , 'fomopress'),
                                    'hide_on_selected' => __('Hide On Selected' , 'fomopress'),
                                ],
                                'toggle' => [
                                    'on_selected' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ],
                                    'hide_on_selected' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ]
                                ],
                                'hide' => [
                                    'everywhere' => [ 
                                        'fields' => [ 'all_locations' ]
                                    ],
                                ],
                            ),
                            'all_locations'  => array(
                                'type'      => 'select',
                                'label'     => __('Locations' , 'fomopress'),
                                'priority'	=> 20,
                                'options'   => FomoPress_Locations::locations(),
                            ),
                            'show_on_display'  => array(
                                'type'      => 'select',
                                'label'     => __('Display' , 'fomopress'),
                                'priority'	=> 200,
                                'options'   => [
                                    'always'          => __('Always' , 'fomopress'),
                                    'logged_out_user' => __('Logged Out User' , 'fomopress'),
                                    'logged_in_user'  => __('Logged In User' , 'fomopress'),
                                ],
                            )
                        ),
                    ),
                )
            ),
            'finalize_tab' => array(
                'title'         => __('Finalize', 'fomopress'),
                'icon'          => 'cog.svg',
                'sections'      => array(
                    
                )
            ),
        ))
    );
}