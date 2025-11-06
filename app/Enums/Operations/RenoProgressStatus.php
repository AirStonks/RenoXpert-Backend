<?php

namespace App\Enums\Operations;

// Per your instructions, this is a FIXED status list.
// We are just locking it in as an Enum.
enum RenoProgressStatus: string
{
    case PRE_PURCHASE = 'pre-purchase';
    case PENDING_VP = 'pending-vp';
    case UNDER_DEFECT = 'under-defect';
    case PENDING_PERMIT = 'pending-permit';
    case P1 = 'p1';
    case P2A = 'p2a';
    case P2B = 'p2b';
    case SCHEDULED_HANDOVER = 'scheduled-handover';
    case SUCCESSFUL_HANDOVER = 'successful-handover';
    case ONBOARDING = 'onboarding';
    case ONBOARDED = 'onboarded';
}
