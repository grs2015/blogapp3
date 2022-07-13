<?php

namespace App\Permissions;

class Permissions
{
    public const CAN_PUBLISH_BLOGPOST = 'can-publish-blogpost';
    public const CAN_PENDING_BLOGPOST = 'can-pending-blogpost';
    public const CAN_UNPUBLISH_BLOGPOST = 'can-unpublish-blogpost';
    public const CAN_REJECT_BLOGPOST = 'can-reject-blogpost';

    public const CAN_SET_FAVORITE_BLOGPOST = 'can-set-favorite-post';
    public const CAN_UNSET_FAVORITE_BLOGPOST = 'can-unset-favorite-post';

    public const CAN_PUBLISH_COMMENT = 'can-publish-comment';
    public const CAN_PENDING_COMMENT = 'can-pending-comment';
    public const CAN_UNPUBLISH_COMMENT = 'can-unpublish-comment';

    public const CAN_RATE_POST = 'can-rate-post';
    public const CAN_COMMENT_POST = 'can-comment-post';

    public const CAN_ALLOW_USER = 'can-allow-user';
    public const CAN_REJECT_USER = 'can-reject-user';
    public const CAN_PENDING_USER = 'can-pending-user';
}
