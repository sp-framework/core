<?php

namespace Apps\Core\Install;

use OpenApi\Attributes as OA;

#[OA\OpenApi(info: new OA\Info(
    title: 'SP Framework Core',
    description: 'SP Framework Core API',
    contact: new OA\Contact(email: 'email@oyeaussie.dev'),
    license: new OA\License(
        name: 'MIT',
        url: 'https://raw.githubusercontent.com/sp-framework/core/refs/heads/main/LICENSE'
    )
), servers: [
    new OA\Server(
        url: 'https://api.sp.oyeaussie.dev/sandbox/',
        description: 'SP Framework API (Sandbox)'
    ),
    new OA\Server(
        url: 'https://api.sp.oyeaussie.dev/',
        description: 'SP Framework API (Production)'
    ),
], externalDocs: new OA\ExternalDocumentation(
    description: 'Find out more about SP Framework',
    url: 'https://sp.oyeaussie.dev/docs/'
))]
class Install
{
}