<?php

namespace App\Repositories;

use PDO;

class GupshupMonitorRepository
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function create(
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
    ): int {
        $sql = '
            INSERT INTO whatsapp_message_errors (
                conta,
                id_conta,
                id_chat,
                numero_origem,
                numero_destino,
                id_flow,
                template,
                id_template,
                id_mailing,
                erro,
                motivo
            ) VALUES (
                :conta,
                :id_conta,
                :id_chat,
                :numero_origem,
                :numero_destino,
                :id_flow,
                :template,
                :id_template,
                :id_mailing,
                :erro,
                :motivo
            )
        ';

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'conta' => $conta,
            'id_conta' => $idConta,
            'id_chat' => $idChat,
            'numero_origem' => $numeroOrigem,
            'numero_destino' => $numeroDestino,
            'id_flow' => $idFlow,
            'template' => $template,
            'id_template' => $idTemplate,
            'id_mailing' => $idMailing,
            'erro' => $erro,
            'motivo' => $motivo
        ]);

        return (int) $this->connection->lastInsertId();
    }
}