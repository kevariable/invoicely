<?php

use App\Mcp\Servers\InvoicelyServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('mcp', InvoicelyServer::class);
