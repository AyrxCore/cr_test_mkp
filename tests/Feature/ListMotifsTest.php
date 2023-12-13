<?php

declare(strict_types=1);

\it('gets list of contact motifs', function () {
    $client = $this::createClient();

    $client->request('GET', '/api/contact/list-motifs');

    $this->assertResponseStatusCodeSame(200);

    $this->assertJsonResponseMatches('list-motifs/response.json');
})->group('list-motifs');
