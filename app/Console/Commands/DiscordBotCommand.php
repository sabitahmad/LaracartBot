<?php

namespace App\Console\Commands;

use App\DiscordCommands\Ping;
use App\DiscordCommands\Shop;
use Carbon\Carbon;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscordBotCommand extends Command
{
    protected $signature = 'd:iscord-bot';

    protected $description = 'Command description';

    /**
     * @throws IntentException
     */
    public function handle(): void
    {
        $discord = new Discord([
            'token' => config('app.discord_bot_token'), // Put your Bot token here from https://discord.com/developers/applications/
            'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT // Required to get message content, enable it on https://discord.com/developers/applications/
        ]);

// When the Bot is ready
        $discord->on('init', function (Discord $discord) {



            $directory = app_path('DiscordCommands');

            if (File::isDirectory($directory)) {

                $files = File::files($directory);

                foreach ($files as $file) {
                    $class = 'App\\DiscordCommands\\' . pathinfo($file, PATHINFO_FILENAME);

                    if (class_exists($class)) {
                        $class::register($discord); // Assuming $discord is already defined
                        Log::info("Registered $class");
                    }
                }
            }

        });

        $discord->run();
    }
}
