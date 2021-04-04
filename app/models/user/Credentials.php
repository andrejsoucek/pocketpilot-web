<?php

namespace PP\User;

interface Credentials
{

    public function getEmail(): string;

    public function getAuthString(): string;
}
