<?php


namespace Zngly\ACFM\Mutations;


class PostObject extends Mutations
{
    public function __construct()
    {
        add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $post_type_object) {
            $this->updater($post_id, $input, $post_type_object->graphql_single_name);
        }, 10, 3);
    }
}
