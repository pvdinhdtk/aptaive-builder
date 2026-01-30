<?php

function aptaive_me()
{
    return aptaive_handle(function () {

        $user = aptaive_auth_user();

        return aptaive_response([
            'id'          => $user->ID,
            'displayName' => $user->display_name,
            'email'       => $user->user_email,
            'avatar'      => get_avatar_url($user->ID),
        ]);
    });
}
