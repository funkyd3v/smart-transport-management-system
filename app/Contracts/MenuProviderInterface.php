<?php

declare(strict_types=1);

namespace App\Contracts;

interface MenuProviderInterface
{
    /**
     * @return array<int, array{
     *     title: string,
     *     items: array<int, array{
     *         name: string,
     *         icon: string,
     *         path?: string,
     *         new?: bool,
     *         pro?: bool,
     *         subItems?: array<int, array{
     *             name: string,
     *             path: string,
     *             new?: bool,
     *             pro?: bool
     *         }>
     *     }>
     * }>
     */
    public function getGroups(): array;
}
