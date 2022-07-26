<?php

namespace Zngly\ACFM;

class MediaItem
{

    public function __construct()
    {
        add_action('graphql_media_item_mutation_update_additional_data', function ($media_item_id, $input, $post_type_object) {
            $this->config = new Config();
            // $this->post_object_mutation_action($media_item_id, $input, $post_type_object);
        }, 10, 3);
    }
}
