<?php


namespace Zngly\ACFM\Mutations;

use Zngly\ACFM\Config;
use Zngly\ACFM\Mutations;


class PostObject
{
    public function __construct()
    {
        add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $post_type_object) {
            Mutations::post_object_mutation_action($post_id, $input, $post_type_object, new Config());
        }, 10, 3);
    }
}
