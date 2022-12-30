<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class OpenAi extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!ask';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 10000;
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message, array $args) {
            if ($this->bailForBotOrDirectMessage($message)) {
                return;
            }

            $message->channel->broadcastTyping();

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->post(
                    url: 'https://api.openai.com/v1/completions',
                    headers: [
                        'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                    body: json_encode([
                        'model' => 'text-davinci-003',
                        'max_tokens' => 3072,
                        'prompt' => Helpers::implodeContent($args),
                    ])
                );

                $result = Helpers::json($response)['choices'][0]['text'];

                foreach (Helpers::splitMessage($result) as $key => $chunk) {
                    if ($key === 0) {
                        $message->reply($chunk);
                    } else {
                        $message->channel->sendMessage($chunk);
                    }
                }
            } catch (Throwable $e) {
                $reply = 'You broke me. Please try again.'.PHP_EOL;
                $reply .= '```diff'.PHP_EOL;
                $reply .= "- {$e->getMessage()}".PHP_EOL;
                $reply .= '```';

                $message->reply($reply);
            }
        }, $message, $args);
    }
}
