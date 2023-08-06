<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Channel\Channel;
use Discord\WebSockets\Intents;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use React\EventLoop\Loop;

class DiscordBotCommand extends Command
{
    protected $signature = 'discord:bot';

    protected $description = 'Command description';

    /**
     * @throws IntentException
     */
    public function handle(): void
    {
        $discord = new Discord([
            'token' => config('discord.discord_bot_token'), // Put your Bot token here from https://discord.com/developers/applications/
            'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT, // Required to get message content, enable it on https://discord.com/developers/applications/
            'loop' => Loop::get(),
        ]);

        $discord->on('init', function (Discord $discord) {

            $loop = $discord->getLoop();

            $previousCategories = []; // Store the previous state of categories

            $loop->addPeriodicTimer(10, function () use ($discord, &$previousCategories) {
                $guild = $discord->guilds->get('id', '1136789819389919365');

                $panelCategories = ProductCategory::orderBy('id')->get();

                foreach ($panelCategories as $panelCategory) {
                    $categoryName = str_replace(' ', '-', strtolower($panelCategory->name));
                    $existingChannel = $guild->channels->filter(function ($existingChannel) use ($categoryName) {
                        return $existingChannel->name === $categoryName && $existingChannel->type === Channel::TYPE_GUILD_TEXT;
                    })->first();

                    if ($existingChannel) {
                        // Check for updates in category model
                        if (
                            isset($previousCategories[$panelCategory->id]) &&
                            ($previousCategories[$panelCategory->id]['name'] !== $panelCategory->name ||
                                $previousCategories[$panelCategory->id]['description'] !== $panelCategory->description)
                        ) {
                            // Update channel properties
                            $existingChannel->name = $categoryName;
                            $existingChannel->topic = $panelCategory->description;
                            $guild->channels->save($existingChannel)->done(function (Channel $channel) {
                                Log::info('Channel updated => '.$channel->id);
                            });
                        }
                    } else {
                        $newChannel = $guild->channels->create([
                            'name' => $categoryName,
                            // All other options are optional
                            'type' => Channel::TYPE_GUILD_TEXT,
                            'topic' => $panelCategory->description,
                            'nsfw' => false,
                            'parent_id' => '1137375609648066671',
                            // more options in Docs
                        ]);

                        $guild->channels->save($newChannel)->done(function (Channel $channel) use ($panelCategory) {
                            Log::info('Channel created => '.$channel->id);
                            $panelCategory->channel_id = $channel->id;
                            $panelCategory->save();
                            $channel->setPermissions($channel->guild->roles->get('name', '@everyone'),
                                [], [
                                    'send_messages', ]);
                        });
                    }

                    // Update the previous state of the category
                    $previousCategories[$panelCategory->id] = [
                        'name' => $panelCategory->name,
                        'description' => $panelCategory->description,
                    ];
                }

                foreach ($guild->channels as $existingChannel) {
                    if ($existingChannel->type === Channel::TYPE_GUILD_TEXT && $existingChannel->parent_id == '1137375609648066671') {

                        $channelExistsModel = $panelCategories->contains(function ($category) use ($existingChannel) {
                            return $category->channel_id === $existingChannel->id;
                        });

                        if (! $channelExistsModel) {
                            $guild->channels->delete($existingChannel->id)->done(function (Channel $channel) {
                                echo 'Deleted channel: ', $channel->name;
                            });
                        }
                    }
                }

            });

            $directory = app_path('DiscordCommands');

            if (File::isDirectory($directory)) {

                $files = File::files($directory);

                foreach ($files as $file) {
                    $class = 'App\\DiscordCommands\\'.pathinfo($file, PATHINFO_FILENAME);

                    if (class_exists($class)) {
                        $class::register($discord);
                        Log::info("Registered $class");
                    }
                }
            }

        });

        $discord->run();
    }
}
