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
            $event['erro']
        );
    }

    private function validateEvent(array $event): array
    {
        $conta = $event['conta'] ?? null;
        $idConta = $event['id_conta'] ?? null;
        $idChat = $event['id_chat'] ?? null;
        $numeroOrigem = $event['numero_origem'] ?? null;
        $numeroDestino = $event['numero_destino'] ?? null;
        $idFlow = $event['id_flow'] ?? null;
        $template = $event['template'] ?? null;
        $idTemplate = $event['id_template'] ?? null;
        $idMailing = $event['id_mailing'] ?? null;
        $erro = $event['erro'] ?? null;
        $motivo = $event['motivo'] ?? null;

        if (!is_string($conta) || trim($conta) === '') {
            throw new InvalidArgumentException(
                'Field "conta" is required'
            );
        }

        if (!is_string($idConta) || trim($idConta) === '') {
            throw new InvalidArgumentException(
                'Field "id_conta" is required'
            );
        }

        if (!is_string($numeroOrigem) || trim($numeroOrigem) === '') {
            throw new InvalidArgumentException(
                'Field "numero_origem" is required'
            );
        }

        if (!is_string($numeroDestino) || trim($numeroDestino) === '') {
            throw new InvalidArgumentException(
                'Field "numero_destino" is required'
            );
        }

        if (!is_string($erro) || trim($erro) === '') {
            throw new InvalidArgumentException(
                'Field "erro" is required'
            );
        }

        $optionalFields = [
            'id_chat' => $idChat,
            'id_flow' => $idFlow,
            'template' => $template,
            'id_template' => $idTemplate,
            'id_mailing' => $idMailing,
            'motivo' => $motivo
        ];

        foreach ($optionalFields as $field => $value) {
            if ($value !== null && (!is_string($value) || trim($value) === '')) {
                throw new InvalidArgumentException(
                    sprintf('Field "%s" must be a non-empty string', $field)
                );
            }
        }

        return [
            'conta' => trim($conta),
            'id_conta' => trim($idConta),
            'id_chat' => $idChat !== null ? trim($idChat) : null,
            'numero_origem' => trim($numeroOrigem),
            'numero_destino' => trim($numeroDestino),
            'id_flow' => $idFlow !== null ? trim($idFlow) : null,
            'template' => $template !== null ? trim($template) : null,
            'id_template' => $idTemplate !== null ? trim($idTemplate) : null,
            'id_mailing' => $idMailing !== null ? trim($idMailing) : null,
            'erro' => trim($erro),
            'motivo' => $motivo !== null ? trim($motivo) : null
        ];
    }

    private function processEvent(
        string $conta,
        string $idConta,
        ?string $idChat,
        string $numeroOrigem,
        string $numeroDestino,
        ?string $idFlow,
        ?string $template,
        ?string $idTemplate,
        ?string $idMailing,
        string $erro,
        ?string $motivo
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