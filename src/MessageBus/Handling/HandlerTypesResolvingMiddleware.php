<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class HandlerTypesResolvingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var IHandlerTypesResolver
	 */
	private $resolver;



	public function __construct(IHandlerTypesResolver $resolver)
	{
		$this->resolver = $resolver;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IBusMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$messageType = MessageType::fromMessage($message);
		$handlerTypes = $this->resolver->resolve($messageType);

		$context = $handlerTypes->saveTo($context);

		return $nextMiddlewareCallback($message, $context);
	}

}
