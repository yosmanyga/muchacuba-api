<?php

namespace Muchacuba;

interface ListenInitFacebookUser
{
    /**
     * @param string $uniqueness
     * @param string $email
     */
    public function listen(
        string $uniqueness,
        string $email
    );
}
