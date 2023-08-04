<?php

namespace App\DiscordCommands;

use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Exception;
use Illuminate\Support\Facades\Log;

class Ping
{
    /**
     * @throws Exception
     */
    public static function register(Discord $discord): void
    {
        $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('ping')
                ->setDescription('pong')
                ->toArray()
            )
        );

        $discord->listenCommand('ping', function (Interaction $interaction) {
            // Respond the /ping command with interaction message "pong!"
            $interaction->respondWithMessage(MessageBuilder::new()->setContent('Pong!'));
        });

    }
}
