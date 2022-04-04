<?php

include __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use Discord\Discord;
use GuzzleHttp\Client;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Builders\MessageBuilder;
use Discord\Builders\Components\Button;
use Discord\Builders\Components\ActionRow;
use Discord\Parts\Interactions\Interaction;

// Create a new Discord instance with the token and prefix from the .env file
$discord = new Discord([
    'token' => $_ENV["DISCORD_TOKEN"],
    'intents' => [
        Intents::GUILDS, Intents::GUILD_BANS, Intents::GUILD_MESSAGES // ...
    ],
    'shardId' => (int) $_ENV["SHARD_ID"],
    'shardCount' => (int) $_ENV["SHARD_COUNT"],
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot is ready!", PHP_EOL;
    $client = new Client();
    // Listen for messages.
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        // check if the message is a command
        $client = new Client(['base_uri' => (string)$_ENV["BASE_URI"]]);
        $msgexplode = explode('.', $message->content);
        //*get Random Ayat
        if($message->content == (string)$_ENV["PREFIX"]."random") {
            try {
                $response = $client->request('GET', 'api/quran/random');
                $data = json_decode($response->getBody());
                $message->reply($data->text . "\n" . $data->translate . "\n\n" . $data->sura . ":" . $data->aya);
            } catch (GuzzleHttp\Exception\RequestException $exception) {
                // $message->reply("something went wrong". "\n" . "please try again later or contact the developer");
                $embed = MessageBuilder::new()
                ->addEmbed(
                    [
                        'title' => 'Error',
                        'description' => 'Please try again later or contact the developer',
                        'color' => hexdec( "f44336" ),
                    ]
                );
                $message->channel->sendMessage($embed);
            }
        }
        //*help command
        if($message->content == (string)$_ENV["PREFIX"]."help") {
            $embed = MessageBuilder::new()
            ->addEmbed(
                [
                    'type' => 'article',
                    'title' => 'Help',
                    'description' => '**svere**',
                    'timestamp' => date('c'),
                    'fields' => [
                        
                        [
                            'name' => '**'.$_ENV["PREFIX"].'random**',
                            'value' => 'Get a random sura and aya',
                            'inline' => true,
                        ],
                        [
                            'name' => "​",
                            'value' => "​",
                            'inline' => true,
                        ],
                        [
                            'name' => '**'.$_ENV["PREFIX"].'help**',
                            'value' => 'Get this help message',
                            'inline' => true,
                        ],
                        [
                            'name' => '**'.$_ENV["PREFIX"].'surah**',
                            'value' => 'get list and number of surah',
                            'inline' => true,
                        ],
                        [
                            'name' => "​",
                            'value' => "​",
                            'inline' => true,
                        ],
                        [
                            'name' => '**'.$_ENV["PREFIX"].'random.ayat.{numbersurah}**',
                            'value' => 'get a random ayat from surah example: '.$_ENV["PREFIX"].'random.ayat.1 to get a random ayat from surah al-fatihah',
                            'inline' => true,
                        ],
                    ],
                    'color' => hexdec( "f44336" ),
                ]
            );
            $message->channel->sendMessage($embed);
        }
        //*if command has 3 words
        if(count($msgexplode) == 3) {
            if($msgexplode[0] == (string)$_ENV["PREFIX"]."random" && $msgexplode[1] == "ayat" && is_numeric($msgexplode[2])) {
                try {
                    $response = $client->request('GET', 'api/quran/random/sura/'.$msgexplode[2]);
                    $data = json_decode($response->getBody());
                    $message->reply($data->text . "\n" . $data->translate . "\n\n" . $data->sura . ":" . $data->aya);
                } catch (GuzzleHttp\Exception\RequestException $exception) {
                    $embed = MessageBuilder::new()
                    ->addEmbed(
                        [
                            'title' => 'Error',
                            'description' => 'Please try again later or contact the developer',
                            'color' => hexdec( "f44336" ),
                        ]
                    );
                    $message->channel->sendMessage($embed);
                }
            }
        }
        //*get list surah
        if($message->content == (string)$_ENV["PREFIX"]."surah") {
            try {
                $embedsurah = MessageBuilder::new()
                 ->addEmbed(
                     [
                        'title' => 'Surah',
                        'description' => '**list of surah**',
                        'fields' => [
                            [
                                'name' => '​',
                                'value' => "
                                1. Al-Fatihah
                                2. Al-Baqarah
                                3. Aali Imran
                                4. An-Nisa’
                                5. Al-Ma’idah
                                6. Al-An’am
                                7. Al-A’raf
                                8. Al-Anfal
                                9. At-Taubah
                                10. Yunus",
                                'inline' => true,
                            ],
                            [
                                'name' => '​',
                                'value' => "
                                11. Hud
                                12. Yusuf
                                13. Ar-Ra’d
                                14. Ibrahim
                                15. Al-Hijr
                                16. An-Nahl
                                17. Al-Isra’
                                18. Al-Kahf
                                19. Maryam
                                20. Ta-Ha",
                                'inline' => true,
                            ],
                            [
                                'name' => '​',
                                'value' => "
                                21. Al-Anbiya’
                                22. Al-Haj
                                23. Al-Mu’minun
                                24. An-Nur
                                25. Al-Furqan
                                26. Ash-Shu’ara’
                                27. An-Naml
                                28. Al-Qasas
                                29. Al-Ankabut
                                30. Ar-Rum",
                                'inline' => true,    
                            ],
                            [
                                'name' => '​',
                                'value' => "
                                31. Luqman
                                32. As-Sajdah
                                33. Al-Ahzab
                                34. Saba’
                                35. Al-Fatir
                                36. Ya-Sin
                                37. As-Saffah
                                38. Sad
                                39. Az-Zumar
                                40. Ghafar",
                                'inline' => true,    
                            ],
                            [
                                'name' => '​',
                                'value' => "
                                41. Fusilat
                                42. Ash-Shura
                                43. Az-Zukhruf
                                44. Ad-Dukhan
                                45. Al-Jathiyah
                                46. Al-Ahqaf
                                47. Muhammad
                                48. Al-Fat’h
                                49. Al-Hujurat",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                50. Qaf
                                51. Adz-Dzariyah
                                52. At-Tur
                                53. An-Najm
                                54. Al-Qamar
                                55. Ar-Rahman
                                56. Al-Waqi’ah
                                57. Al-Hadid
                                58. Al-Mujadilah
                                59. Al-Hashr",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                60. Al-Mumtahanah
                                61. As-Saf
                                62. Al-Jum’ah
                                63. Al-Munafiqun
                                64. At-Taghabun
                                65. At-Talaq
                                66. At-Tahrim
                                67. Al-Mulk
                                68. Al-Qalam
                                69. Al-Haqqah
                                70. Al-Ma’arij",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                71. Nuh
                                72. Al-Jinn
                                73. Al-Muzammil
                                74. Al-Mudaththir
                                75. Al-Qiyamah
                                76. Al-Insan
                                77. Al-Mursalat 
                                78. An-Naba’
                                79. An-Nazi’at
                                80. ‘Abasa",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                81. At-Takwir
                                82. Al-Infitar
                                83. Al-Mutaffifin
                                84. Al-Inshiqaq
                                85. Al-Buruj
                                86. At-Tariq
                                87. Al-A’la
                                88. Al-Ghashiyah 
                                89. Al-Fajr 
                                90. Al-Balad",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                91. Ash-Shams
                                92. Al-Layl
                                93. Adh-Dhuha
                                94. Al-Inshirah
                                95. At-Tin
                                96. Al-‘Alaq
                                97. Al-Qadar
                                98. Al-Bayinah
                                99. Az-Zalzalah
                                100. Al-‘Adiyah",
                                'inline' => true,

                            ],
                            [
                                'name' => '​',
                                'value' => "
                                101. Al-Qari’ah
                                102. At-Takathur
                                103. Al-‘Asr
                                104. Al-Humazah
                                105. Al-Fil
                                106. Quraish
                                107. Al-Ma’un
                                108. Al-Kauthar
                                109. Al-Kafirun
                                110. An-Nasr",
                                'inline' => true,
                            ],
                            [
                                'name' => '​',
                                'value' => "
                                111. Al-Masad
                                112. Al-Ikhlas
                                113. Al-Falaq
                                114. An-Nas",
                                'inline' => true,
                            ],
                            ],
                        'color' => hexdec( "f44336" ),
                        ]
                        );

                $message->channel->sendMessage($embedsurah);
            } catch (GuzzleHttp\Exception\RequestException $exception) {
                $embed = MessageBuilder::new()
                ->addEmbed(
                    [
                        'title' => 'Error',
                        'description' => 'Please try again later or contact the developer',
                        'color' => hexdec( "f44336" ),
                    ]
                );
                $message->channel->sendMessage($embed);
            }
        }
        
        
    });
   
});

$discord->run();