<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Auto Presenter.
 *
 * (c) Shawn McCool <shawn@heybigname.com>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McCool\Tests\Facades;

use GrahamCampbell\TestBenchCore\FacadeTrait;
use McCool\LaravelAutoPresenter\AutoPresenter;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter as Facade;
use McCool\Tests\AbstractTestCase;

class AutoPresenterTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'autopresenter';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected static function getFacadeClass(): string
    {
        return Facade::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected static function getFacadeRoot(): string
    {
        return AutoPresenter::class;
    }
}
