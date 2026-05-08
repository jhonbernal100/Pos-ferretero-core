<?php

return [
    'numero' => trim(shell_exec('git rev-list --count HEAD') ?? '0'),
];