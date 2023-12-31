<?php

namespace App\DiscordCommands;

use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class Shop
{
    /**
     * @throws \Exception
     */
    public static function register(Discord $discord): void
    {
        $discord->application->commands->save(

            $discord->application->commands->create(CommandBuilder::new()
                ->setName('shop')
                ->setDescription('shop')
                ->toArray()
            )
        );

        $discord->listenCommand('shop', function (Interaction $interaction) {
            // Respond the /ping command with interaction message "pong!"
            $interaction->respondWithMessage(MessageBuilder::new()->setContent('shop!'));
        });

    }
}
