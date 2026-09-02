<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

logout();

header('Location: ../login.html', true, 303);
exit;
