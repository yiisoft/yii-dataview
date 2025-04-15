<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;

final class UrlCreatorTest extends TestCase
{
	public function testInvoke(): void
	{
		$arguments = ['page' => 2];
		$queryParameters = ['sort' => 'name', 'dir' => 'asc'];
		$expectedUrl = '/users?page=2&sort=name&dir=asc';

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

		$urlGenerator
			->expects($this->once())
			->method('generateFromCurrent')
			->with($arguments, $queryParameters)
			->willReturn($expectedUrl);

        /** @var UrlGeneratorInterface $urlGenerator */
		$urlCreator = new UrlCreator($urlGenerator);

		$this->assertSame($expectedUrl, $urlCreator($arguments, $queryParameters));
	}

	public function testInvokeWithEmptyParameters(): void
	{
		$expectedUrl = '/users';

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

		$urlGenerator->expects($this->once())->method('generateFromCurrent')->with([], [])->willReturn($expectedUrl);

        /** @var UrlGeneratorInterface $urlGenerator */
		$urlCreator = new UrlCreator($urlGenerator);

		$this->assertSame($expectedUrl, $urlCreator([], []));
	}

	public function testInvokeWithOnlyArguments(): void
	{
		$arguments = ['id' => 123];
		$expectedUrl = '/users/123';

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

		$urlGenerator
			->expects($this->once())
			->method('generateFromCurrent')
			->with($arguments, [])
			->willReturn($expectedUrl);

        /** @var UrlGeneratorInterface $urlGenerator */
		$urlCreator = new UrlCreator($urlGenerator);

		$this->assertSame($expectedUrl, $urlCreator($arguments, []));
	}

	public function testInvokeWithOnlyQueryParameters(): void
	{
		$queryParameters = ['filter' => 'active'];
		$expectedUrl = '/users?filter=active';

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

		$urlGenerator
			->expects($this->once())
			->method('generateFromCurrent')
			->with([], $queryParameters)
			->willReturn($expectedUrl);

        /** @var UrlGeneratorInterface $urlGenerator */
		$urlCreator = new UrlCreator($urlGenerator);

		$this->assertSame($expectedUrl, $urlCreator([], $queryParameters));
	}
}
