<?php
bx_import('BxDolAlerts');

class SkSocialLoginDeleteProfileResponse extends BxDolAlertsResponse
{
    function response ($oTag)
    {
        if (!($iProfileId = (int)$oTag->iObject))
            return;
        bx_import('BxDolService');
        BxDolService::call('social_login', 'delete_profile_connected', array($iProfileId));
    }
}
?>