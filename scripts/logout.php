<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

auth_logout();

header('Location: ../login.php');
exit;