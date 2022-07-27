<?php

namespace Zngly\ACFM;

use WPGraphQL\Utils\Utils as WpGraphqlUtils;

class Utils
{
    public static function mapPostIdsFromGids($gids)
    {
        if (is_array($gids)) {
            $ids = [];
            foreach ($gids as $gid) {
                $ids[] = WpGraphqlUtils::get_database_id_from_id($gid);
            }
            return $ids;
        } else {
            return WpGraphqlUtils::get_database_id_from_id($gids);
        }
    }

    public static function getPostIdsFromGids($gids)
    {
        $postIds = [];
        foreach ($gids as $gid) {
            $postIds[] = WpGraphqlUtils::get_database_id_from_id($gid);
        }

        return $postIds;
    }

    public static function getPostIdFromGid($gids)
    {
        return WpGraphqlUtils::get_database_id_from_id($gids);
    }
}
