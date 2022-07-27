<?php

namespace Zngly\ACFM\Mutations;

use Zngly\ACFM\Mutations;

class MediaItem
{

    public function __construct()
    {
        add_action('graphql_media_item_mutation_update_additional_data', function ($media_item_id, $input, $post_type_object) {
            Mutations::updater($media_item_id, $input, $post_type_object->graphql_single_name);
        }, 10, 3);
    }
}
