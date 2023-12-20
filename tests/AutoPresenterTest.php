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

namespace McCool\Tests;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use McCool\LaravelAutoPresenter\AutoPresenter;
use McCool\LaravelAutoPresenter\Decorators\DecoratorInterface;
use McCool\LaravelAutoPresenter\Exceptions\PresenterNotFoundException;
use McCool\Tests\Stubs\DecoratedAtom;
use McCool\Tests\Stubs\DecoratedAtomPresenter;
use McCool\Tests\Stubs\DependencyDecoratedAtom;
use McCool\Tests\Stubs\DependencyDecoratedAtomPresenter;
use McCool\Tests\Stubs\UndecoratedAtom;
use McCool\Tests\Stubs\WronglyDecoratedAtom;
use Mockery;

class AutoPresenterTest extends AbstractTestCase
{
    private $autoPresenter;

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $this->autoPresenter = $app->make(AutoPresenter::class);
    }

    public function testRegistration()
    {
        $autoPresenter = new AutoPresenter();

        $decorator = Mockery::mock(DecoratorInterface::class);

        $autoPresenter->register($decorator);

        $this->assertSame([$decorator], $autoPresenter->getDecorators());
    }

    public function testWontDecorateOtherObjects()
    {
        $atom = new UndecoratedAtom();
        $decoratedAtom = $this->autoPresenter->decorate($atom);

        $this->assertInstanceOf(UndecoratedAtom::class, $decoratedAtom);
    }

    public function testDecoratesAtom()
    {
        $atom = $this->getDecoratedAtom();
        $decoratedAtom = $this->autoPresenter->decorate($atom);

        $this->assertInstanceOf(DecoratedAtomPresenter::class, $decoratedAtom);
    }

    public function testDecoratesAtomWithDependencies()
    {
        $atom = $this->getDependencyDecoratedAtom();
        $decoratedAtom = $this->autoPresenter->decorate($atom);

        $this->assertInstanceOf(DependencyDecoratedAtomPresenter::class, $decoratedAtom);
    }

    public function testDecoratesPaginator()
    {
        $paginator = $this->getFilledPaginator();
        $decoratedPaginator = $this->autoPresenter->decorate($paginator);

        $this->assertCount(5, $decoratedPaginator);

        foreach ($decoratedPaginator as $decoratedAtom) {
            $this->assertInstanceOf(DecoratedAtomPresenter::class, $decoratedAtom);
        }
    }

    public function testDecorateCollection()
    {
        $collection = $this->getFilledCollection();
        $decoratedCollection = $this->autoPresenter->decorate($collection);

        $this->assertCount(5, $decoratedCollection);

        foreach ($decoratedCollection as $decoratedAtom) {
            $this->assertInstanceOf(DecoratedAtomPresenter::class, $decoratedAtom);
        }
    }

    public function testWronglyDecoratedClassThrowsException()
    {
        $class = 'ThisClassDoesntExistAnywhereInTheKnownUniverse';
        $this->expectException(PresenterNotFoundException::class);
        $this->expectExceptionMessage("The presenter class '$class' was not found.");

        try {
            $atom = new WronglyDecoratedAtom();
            $this->autoPresenter->decorate($atom);
        } catch (PresenterNotFoundException $e) {
            $this->assertSame($class, $e->getClass());

            throw $e;
        }
    }

    private function getDecoratedAtom()
    {
        return new DecoratedAtom();
    }

    private function getDependencyDecoratedAtom()
    {
        return new DependencyDecoratedAtom();
    }

    private function getFilledPaginator()
    {
        $items = [];

        foreach (range(1, 5) as $i) {
            $items[] = $this->getDecoratedAtom();
        }

        return new Paginator($items, 5);
    }

    private function getFilledCollection()
    {
        $items = [];

        foreach (range(1, 5) as $i) {
            $items[] = $this->getDecoratedAtom();
        }

        return new Collection($items);
    }
}
