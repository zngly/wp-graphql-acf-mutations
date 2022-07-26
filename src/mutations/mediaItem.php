<?php

namespace Zngly\ACFM\Mutations;

use Zngly\ACFM\Config;
use Zngly\ACFM\Mutations;

class MediaItem
{

    public function __construct()
    {
        add_action('graphql_media_item_mutation_update_additional_data', function ($media_item_id, $input, $post_type_object) {
            Mutations::post_object_mutation_action($media_item_id, $input, $post_type_object, new Config());
        }, 10, 3);
    }
}
