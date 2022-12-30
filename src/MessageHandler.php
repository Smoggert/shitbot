<?php

namespace ShitwareLtd\Shitbot;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Support\Helpers;

class MessageHandler
{
    /**
     * @param  Message  $message
     */
    public function __construct(
        private readonly Message $message
    ){}

    /**
     * @return void
     */
    public function __invoke(): void
    {
        if (! Helpers::shouldProceed($this->message)) {
            return;
        }

        $content = Str::lower($this->message->content);

        $this->reactToGoodTimes($content);
        $this->reactToFunny($content);
        $this->reactToThink($content);
        $this->reactToBadWords($content);
        $this->reactToTrongate($content);
        $this->reactToYaz($content);
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToGoodTimes(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: ['nice', 'awesome', 'sweet', 'cool', 'pog', 'yeet', 'neat', 'badass', 'whoa', 'wow']
        ) && Helpers::gamble()) {
            $this->message->react(Shitbot::emoji('cool'));
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToFunny(string $content): void
    {
        if (Str::contains(
                haystack: $content,
                needles: ['lmao', 'lmfao', 'rofl', 'kek', 'cringe', 'funny', 'hah', 'lolol', 'xd']
            ) && Helpers::gamble()) {
            $this->message->react(Shitbot::emoji('funny'));
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToThink(string $content): void
    {
        if (Str::contains(
                haystack: $content,
                needles: ['hmm', 'huh', 'interesting', 'think', 'thonk', 'curious', 'why', 'how', 'who', 'wonder']
            ) && Helpers::gamble()) {
            $this->message->react(Shitbot::emoji('think'));
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToBadWords(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: ['fuck', 'asshole', 'bitch', 'cunt', 'shit', 'pussy', 'dildo', 'dick', 'dumb', 'twat', 'piss', 'bastard', 'prick', 'wanker', 'cock']
        ) && Helpers::gamble()) {
            $this->message->react(Shitbot::emoji('rage'));
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToTrongate(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: 'trongate'
        )) {
            $this->message->react(':trongate:1030233313266389062');
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToYaz(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: 'yaz'
        )) {
            $this->message->react(':FeelsYazMan:1056419745898971176');
        }
    }
}
