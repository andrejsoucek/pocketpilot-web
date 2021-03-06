<?php

declare(strict_types=1);

namespace PP\User;

/**
 * @author Andrej Souček
 */
class UserDatabaseDef
{

    public const
        TABLE_NAME = 'users',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'username',
        COLUMN_EMAIL = 'email',
        COLUMN_FB_UID = 'fb_uid',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_ROLE = 'role',
        COLUMN_TOKEN = 'token';
}
