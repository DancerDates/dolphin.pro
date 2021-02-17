<?php
bx_import('BxDolAlerts');

class SkSocialPostingDeleteProfileResponse extends BxDolAlertsResponse
{
    function response ($oTag)
    {
        if (!($iProfileId = (int)$oTag->iObject))
            return;
        bx_import('BxDolService');
        BxDolService::call('social_posting', 'delete_profile_connected', array($iProfileId));
    }
}
?>