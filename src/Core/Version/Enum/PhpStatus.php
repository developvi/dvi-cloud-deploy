<?php

namespace DVICloudDeploy\Core\Version\Enum;

enum PhpStatus: string
{
    /** Full active support — bug fixes and security updates. */
    case Supported = 'supported';

    /** Active support ended; security fixes only. */
    case SecurityOnly = 'security_only';

    /** No longer supported — display/policy metadata only for now. */
    case EndOfLife = 'end_of_life';

    /** Old install path (pre-unified bash scripts). */
    case Legacy = 'legacy';
}
