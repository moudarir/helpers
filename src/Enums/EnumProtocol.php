<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Enums;

enum EnumProtocol: string
{

    case HTTP = 'http';
    case HTTPS = 'https';
    case FTP = 'ftp';
    case FTPS = 'ftps';
    case WS = 'ws';
    case WSS = 'wss';
}
