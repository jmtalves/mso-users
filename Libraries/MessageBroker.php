<?php

namespace Libraries;



use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpLib\AmqpContext;

/**
 * MessageBroker
 */
class MessageBroker
{

    /**
     * sendMessage
     * @param string $event
     * @param array $data
     */
    public function sendMessage(string $event, array $data)
    {
        $mbroker_ip = getenv('HOST_MESSAGEBROKER');
        $mbroker_user = getenv('USER_MESSAGEBROKER');
        $mbroker_pass = getenv('PASS_MESSAGEBROKER');
        $connectionFactory = new AmqpConnectionFactory('amqp://' . $mbroker_user . ':' . $mbroker_pass . '@' . $mbroker_ip . ':5672');
        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($event);
        $producer = $context->createProducer();
        $message = $context->createMessage(json_encode($data));
        $producer->send($topic, $message);
    }

    /**
     * processMessage
     * @param string $event
     * @param string $name_func
     */
    public function processMessage(string $event, string $name_func)
    {
        $mbroker_ip = getenv('HOST_MESSAGEBROKER');
        $mbroker_user = getenv('USER_MESSAGEBROKER');
        $mbroker_pass = getenv('PASS_MESSAGEBROKER');
        $connectionFactory = new AmqpConnectionFactory('amqp://' . $mbroker_user . ':' . $mbroker_pass . '@' . $mbroker_ip . ':5672');
        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($event);
        $consumer = $context->createConsumer($topic);
        while (true) {
            $message = $consumer->receive();
            if ($message !== null) {
                $body = json_decode($message->getBody(), true);

                call_user_func($name_func, $body);

                $consumer->acknowledge($message);
            }
        }
    }
}
