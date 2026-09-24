<?php

namespace App\Services;

use App\Repositories\GupshupMonitorRepository;
use InvalidArgumentException;

class GupshupMonitorService
{
    public function __construct(
        private GupshupMonitorRepository $repository
    ) {
    }

    public function store(array $payload): array
    {
        $event = $this->validateEvent($payload);

        return $this->processEvent(
            $event['conta'],
            $event['id_conta'],
            $event['id_chat'],
            $event['numero_origem'],
            $event['numero_destino'],
            $event['id_flow'],
            $event['template'],
            $event['id_template'],
            $event['id_mailing'],
            $event['erro'],
            $event['motivo']
        );
    }

    private function validateEvent(array $event): array
    {
        return [
            'conta' => $this->parseField($event['conta'] ?? null),
            'id_conta' => $this->parseField($event['id_conta'] ?? null),
            'id_chat' => $this->parseField($event['id_chat'] ?? null),
            'numero_origem' => $this->parseField($event['numero_origem'] ?? null),
            'numero_destino' => $this->parseField($event['numero_destino'] ?? null),
            'id_flow' => $this->parseField($event['id_flow'] ?? null),
            'template' => $this->parseField($event['template'] ?? null),
            'id_template' => $this->parseField($event['id_template'] ?? null),
            'id_mailing' => $this->parseField($event['id_mailing'] ?? null),
            'erro' => $this->parseField($event['erro'] ?? null),
            'motivo' => $this->parseField($event['motivo'] ?? null)
        ];
    }

    private function parseField(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        return null;
    }

    private function processEvent(
        ?string $conta = null,
        ?string $idConta = null,
        ?string $idChat = null,
        ?string $numeroOrigem = null,
        ?string $numeroDestino = null,
        ?string $idFlow = null,
        ?string $template = null,
        ?string $idTemplate = null,
        ?string $idMailing = null,
        ?string $erro = null,
        ?string $motivo = null
    ): array {
        $id = $this->repository->create(
            $conta,
            $idConta,
            $idChat,
            $numeroOrigem,
            $numeroDestino,
            $idFlow,
            $template,
            $idTemplate,
            $idMailing,
            $erro,
            $motivo
        );

        return [
            'id' => $id,
            'conta' => $conta,
            'action' => 'created'
        ];
    }
}