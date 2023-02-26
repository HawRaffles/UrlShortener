<?php

namespace Tars\UrlShortener\Interfaces;

use InvalidArgumentException;

interface ICheckUrl
{
    /**
     * @return void
     */
    public function GetInput(): string;

    /**
     * @param string $url
     * @throws InvalidArgumentException
     * @return bool
     */
    public function CheckUrl(string $url): bool;
}
