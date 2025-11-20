<?php

test('returns a redirect response for guests', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
