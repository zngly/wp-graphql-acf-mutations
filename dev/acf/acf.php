<?php

// load acf configuration here

// taxonomy

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_62dff4db33699',
        'title' => 'Taxonomy',
        'fields' => array(
            array(
                'key' => 'field_62dff4ea7e7f4',
                'label' => 'taxonomy test',
                'name' => 'taxonomy_test',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'show_in_graphql' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'all',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_graphql' => 1,
        'graphql_field_name' => 'fields',
        'map_graphql_types_from_location_rules' => 0,
        'graphql_types' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_63593a040518f',
        'title' => 'zngly',
        'fields' => array(
            array(
                'key' => 'field_63593a189bad9',
                'label' => 'zngly description',
                'name' => 'zngly_description',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'show_in_graphql' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_63593cb705011',
                'label' => 'zngly google maps',
                'name' => 'zngly_google_maps',
                'type' => 'google_map',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'show_in_graphql' => 1,
                'height' => '',
                'center_lat' => '',
                'center_lng' => '',
                'zoom' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_graphql' => 1,
        'graphql_field_name' => 'postMeta',
        'map_graphql_types_from_location_rules' => 0,
    ));


endif;
