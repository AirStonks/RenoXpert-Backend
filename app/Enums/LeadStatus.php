<?php

namespace App\Enums;

// For `campaign_leads`. This is a standard CRM flow.
enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case CONVERTED = 'converted';
    case NOT_INTERESTED = 'not_interested';
}
