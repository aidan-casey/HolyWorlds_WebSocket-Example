<?php

/*
	Keep in mind while reading this file, everything will be database based so... yeah.
	
	See: http://socketo.me/docs/push for some thoughts on how to do the chat room.
	
	Also, not a fan of these double quotes but all the newlines... ;)
*/

namespace HolyWorlds;

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    
    public function __construct() {
        $this->clients = new \SplObjectStorage();
    }
	
	/**
	 * onOpen
	 * Called when a client opens the connection.
	 * @param ConnectionInterface $con
	 */
    public function onOpen(ConnectionInterface $con) {
        $this->clients->attach($con);
        
		/*
			Here we'd return the last 50 or so chats from the database, just to give them some context.
		*/
		
        echo 'New connection! (' . $con->resourceId . ')' . "\n";
    }
    
	/**
	 * onMessage
	 * Called when a message is sent from the client.
	 * @param $from
	 * @param $msg
	 */
    public function onMessage(ConnectionInterface $from, $msg) {
		
		/*
			Obviously here, we'd write to the database all the info by decoding the JSON message.
		*/
		
		/*
			Decode JSON for our use.
			We could then output to database, etc.
		*/
        $chat = json_decode($msg);
		
		// Output chat in the console
		echo $chat->username . " said: " . $chat->content . "\n";
		
		/*
			Go through online users, if they aren't the sender, send the message.
			In all reality we could just send the message back to the sender but I don't think that would make the most sense.
		*/
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

	/**
	 * onClose
	 * Called when a user closes their connection.
	 * @param ConnectionInterface $con
	 */
    public function onClose(ConnectionInterface $con) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($con);

        echo "Connection {$con->resourceId} has disconnected\n";
    }
	
	/**
	 * onError
	 * Called on a WebSocket error.
	 * @param ConnectionInterface $con.
	 * @param Exception $e
	 */
    public function onError(ConnectionInterface $con, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $con->close();
    }
    
}