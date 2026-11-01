<?php

namespace Kalakotra\Baudigit\Extensions;

use PharIo\Manifest\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Core\Extension;

class MemberExtension extends Extension
{

    private static $db = [
        'Active' => 'Boolean',
        'MemberColor' => 'Varchar(7)' // Hex color code for the member
    ];
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (!$this->owner->MemberColor) {
            // generate a random hex color code
            $this->owner->MemberColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        // if active is false, set LockedOutUntil to +100 years
        if (!$this->owner->Active) {
            $this->owner->LockedOutUntil = date('Y-m-d H:i:s', strtotime('+100 years'));
        } else {
            $this->owner->LockedOutUntil = null;
        }
    }
}