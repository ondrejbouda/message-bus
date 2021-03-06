<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessageHandler;



interface IHandlerProvider
{

	/**
	 * @param HandlerType $type
	 * @return IMessageHandler
	 * @throws HandlerCannotBeProvidedException
	 */
	public function get(HandlerType $type) : IMessageHandler;

}
