<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use Damejidlo\MessageBus\MiddlewareSupportingMessageBus;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class MiddlewareSupportingMessageBusTest extends DjTestCase
{

	public function testHandleWithCorrectOrder() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MiddlewareSupportingMessageBus($middlewareCallbackChainCreator);

		$message = $this->mockMessage();

		$middleware = $this->mockMiddleware();
		$messageBus->appendMiddleware($middleware);

		$callbackChainCalled = FALSE;
		$result = 'some-result';

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->withArgs(function (array $actualMiddleware, \Closure $endChainWithCallback) use ($middleware) : bool {
				Assert::same([$middleware], $actualMiddleware);
				return TRUE;
			})->andReturn(function (IBusMessage $actualMessage) use ($message, &$callbackChainCalled, $result) {
				$callbackChainCalled = TRUE;
				Assert::same($message, $actualMessage);

				return $result;
			});

		$actualResult = $messageBus->handle($message);
		Assert::same($result, $actualResult);

		Assert::true($callbackChainCalled);
	}



	public function testHandleFails() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MiddlewareSupportingMessageBus($middlewareCallbackChainCreator);

		$exception = new \Exception();

		$message = $this->mockMessage();

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->andReturn(function (IBusMessage $actualMessage) use ($exception) : void {
				throw $exception;
			});

		$actualException = Assert::exception(function () use ($messageBus, $message) : void {
			$messageBus->handle($message);
		}, \Exception::class);
		Assert::same($exception, $actualException);
	}



	/**
	 * @return MiddlewareCallbackChainCreator|MockInterface
	 */
	private function mockMiddlewareCallbackChainCreator() : MiddlewareCallbackChainCreator
	{
		$mock = Mockery::mock(MiddlewareCallbackChainCreator::class);

		return $mock;
	}



	/**
	 * @return IBusMessage|MockInterface
	 */
	private function mockMessage() : IBusMessage
	{
		$mock = Mockery::mock(IBusMessage::class);

		return $mock;
	}



	/**
	 * @return IMessageBusMiddleware|MockInterface
	 */
	private function mockMiddleware() : IMessageBusMiddleware
	{
		$mock = Mockery::mock(IMessageBusMiddleware::class);

		return $mock;
	}

}



(new MiddlewareSupportingMessageBusTest())->run();