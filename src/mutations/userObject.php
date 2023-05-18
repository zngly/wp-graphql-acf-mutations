<?php

namespace Zngly\ACFM\Mutations;

use Zngly\ACFM\Mutations;

class UserObject
{
    public function __construct()
    {
        add_action('graphql_user_object_mutation_update_additional_data', function ($user_id, $input, $mutation_name, $context, $info) {
            Mutations::updater($user_id, $input, 'User');
        }, 10, 5);
    }
}
