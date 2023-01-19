<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use ShitwareLtd\Shitbot\Bank\Bank;
use ShitwareLtd\Shitbot\Bank\Item;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Art extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!art';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 120000;
    }

    /**
     * @param  Message|Interaction  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Message|Interaction $entity, array $args): void
    {
        coroutine(function (Message|Interaction $entity, array $args) {
            if ($this->skip($entity)) {
                return;
            }

            $message = Helpers::getMessage($entity);
            $user = Helpers::getUser($entity);

            $this->hitCooldown($user);

//            $message->channel->broadcastTyping();
//
//            $typing = Loop::addPeriodicTimer(
//                interval: 4,
//                callback: fn () => $message->channel->broadcastTyping()
//            );

            try {
//                /** @var ResponseInterface $response */
//                $response = yield Shitbot::browser()
//                    ->withTimeout(60.0)
//                    ->withRejectErrorResponse(false)
//                    ->post(
//                        url: 'https://api.openai.com/v1/images/generations',
//                        headers: [
//                            'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
//                            'Content-Type' => 'application/json',
//                        ],
//                        body: json_encode([
//                            'n' => 1,
//                            'prompt' => Helpers::implodeContent($args),
//                            'response_format' => 'b64_json',
//                            'size' => '1024x1024',
//                        ])
//                    );

//                $result = Helpers::json($response);

//                if ($response->getStatusCode() === 200) {
                    $message->channel->sendMessage(
                        $this->buildMessage(
                            entity: $entity,
                            result: [],
                            args: $args
                        )
                    );

//                    Bank::for($user)->charge(
//                        item: Item::Dalle2,
//                        units: 1
//                    );

                    $this->hitCooldown($user);
//                } else {
//                    $this->clearCooldown($user);
//
//                    $entity->reply($this->formatError(
//                        $result['error']['message']
//                    ));
//                }
            } catch (Throwable $e) {
                $this->clearCooldown($user);

                $entity->reply($this->formatError(
                    $e->getMessage()
                ));
            }

//            Loop::cancelTimer($typing);
        }, $entity, $args);
    }

    /**
     * @param  Message|Interaction  $entity
     * @param  array  $result
     * @param  array  $args
     * @return MessageBuilder
     */
    private function buildMessage(Message|Interaction $entity, array $result, array $args): MessageBuilder
    {
        $builder = MessageBuilder::new();

//        $builder->addFileFromContent(
//            filename: 'dalle_'.uniqid(more_entropy: true).'.png',
//            content: base64_decode($result['data'][0]['b64_json'])
//        );

        iF ($entity instanceof Message) {
            $builder->addComponent(
                $this->buildActionRow(
                    args: $args,
                    withRetry: true
                )
            )->setReplyTo($entity);
        } else {
            $builder->addComponent(
                $this->buildActionRow(
                    args: $args,
                    withRetry: false
                )
            )->setContent("<@{$entity->user->id}>");
        }

        return $builder;
    }

    /**
     * @param  array  $args
     * @param  bool  $withRetry
     * @return ActionRow
     */
    private function buildActionRow(array $args, bool $withRetry): ActionRow
    {
        $action = ActionRow::new();

        if ($withRetry) {
            $action->addComponent(
                Button::new(Button::STYLE_PRIMARY)
                    ->setLabel('Try Prompt Again')
                    ->setListener(
                        callback: fn (Interaction $interaction) => Shitbot::command(Art::class)->handle(
                            entity: $interaction,
                            args: $args
                        ),
                        discord: Shitbot::discord()
                    )
            );
        }

        $action->addComponent(
            Button::new(Button::STYLE_SUCCESS)
                ->setLabel('Generate Variation')
                ->setListener(
                    callback: fn (Interaction $interaction) => Shitbot::command(Variation::class)->handle(
                        entity: $interaction,
                        args: $args
                    ),
                    discord: Shitbot::discord()
                )
        );

        return $action;
    }
}
