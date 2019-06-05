<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IMessage;



final class SubscriberSpecificDomainEvent implements IMessage
{

	/**
	 * @var IDomainEvent
	 */
	private $event;

	/**
	 * @var string
	 */
	private $subscriberType;



	public function __construct(IDomainEvent $event, string $subscriberType)
	{
		$this->event = $event;
		$this->subscriberType = $subscriberType;
	}



	public function getEvent() : IDomainEvent
	{
		return $this->event;
	}



	public function getSubscriberType() : string
	{
		return $this->subscriberType;
	}

}
