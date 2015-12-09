<?php

final class BBBManager_Config_Defines {
    /* ROOM STATES */

    public static $ROOM_WAITING = 1;
    public static $ROOM_OPENED = 2;
    public static $ROOM_CLOSED = 3;

    /* ROOM PROFILE */
    public static $ROOM_ADMINISTRATOR_PROFILE = 1;
    public static $ROOM_MODERATOR_PROFILE = 2;
    public static $ROOM_PRESENTER_PROFILE = 3;
    public static $ROOM_ATTENDEE_PROFILE = 4;

    /* SYSTEM PROFILES */
    public static $SYSTEM_ADMINISTRATOR_PROFILE = 1;
    public static $SYSTEM_SUPPORT_PROFILE = 2;
    public static $SYSTEM_PRIVILEGED_USER_PROFILE = 3;
    public static $SYSTEM_USER_PROFILE = 4;
    public static $NA_PROFILE = 5;

    /* AUTH MODES */
    public static $LOCAL_AUTH_MODE = 1;
    public static $LDAP_AUTH_MODE = 2;
    public static $PERSONA_AUTH_MODE = 3;

    /* MEETING ROOM PRIVACY POLICY */
    public static $PUBLIC_MEETING_ROOM = 1;
    public static $ANY_LOGGED_USER_MEETING_ROOM = 2;
    public static $ONLY_INVITED_USERS_MEETING_ROOM = 3;

    /* ACL RESOURCES */
    public static $ACL_ROOM_RESOURCE = 'rooms';
    public static $ACL_ROOM_LOGS_RESOURCE = 'room-logs';
    public static $ACL_ROOM_AUDIENCE_RESOURCE = 'rooms-audience';
    public static $ACL_ROOM_INVITES_RESOURCE = 'room-invites';
    public static $ACL_ROOM_ACTIONS_RESOURCE = 'room-actions';
    public static $ACL_USER_RESOURCE = 'users';
    public static $ACL_GROUP_RESOURCE = 'groups';
    public static $ACL_SPEED_PROFILES_RESOURCE = 'speed-profiles';
    public static $ACL_ACCESS_PROFILES_RESOURCE = 'access-profiles';
    public static $ACL_TAG_RESOURCE = 'record-tags';
    public static $ACL_MY_ROOMS_RESOURCE = 'my-rooms';
    public static $ACL_ACCESS_RECORDING_RESOURCE = 'recordings-access';
    public static $ACL_RECORDINGS_RESOURCE = 'recordings';
    public static $ACL_CATEGORY_RESOURCE = 'categories';
    public static $ACL_INVITE_TEMPLATE_RESOURCE = 'invite-templates';
    public static $ACL_MAINTENANCE_RESOURCE = 'maintenance';
    public static $ACL_ACCESS_LOGS_RESOURCE = 'access-logs';
    public static $ACL_ACCESS_LOG_DESCRIPTIONS_RESOURCE = 'access-log-descriptions';
    public static $ACL_TIMEZONE_RESOURCE = 'timezones';
    public static $ACL_BBB_CONFIGS_RESOURCE = 'bbb-configs';

    /* Content Types */
    public static $CONTENT_TYPE_CSV = 'text/csv';
    //public static $CONTENT_TYPE_PDF = 'text/csv';
    public static $CONTENT_TYPE_PDF = 'application/pdf';

    public static function getAclResources() {
        return array(
            self::$ACL_ROOM_RESOURCE,
            self::$ACL_ROOM_LOGS_RESOURCE,
            self::$ACL_ROOM_AUDIENCE_RESOURCE,
            self::$ACL_ROOM_INVITES_RESOURCE,
            self::$ACL_ROOM_ACTIONS_RESOURCE,
            self::$ACL_USER_RESOURCE,
            self::$ACL_GROUP_RESOURCE,
            self::$ACL_SPEED_PROFILES_RESOURCE,
            self::$ACL_ACCESS_PROFILES_RESOURCE,
            self::$ACL_TAG_RESOURCE,
            self::$ACL_MY_ROOMS_RESOURCE,
            self::$ACL_ACCESS_RECORDING_RESOURCE,
            self::$ACL_RECORDINGS_RESOURCE,
            self::$ACL_CATEGORY_RESOURCE,
            self::$ACL_INVITE_TEMPLATE_RESOURCE,
            self::$ACL_MAINTENANCE_RESOURCE,
            self::$ACL_ACCESS_LOGS_RESOURCE,
            self::$ACL_ACCESS_LOG_DESCRIPTIONS_RESOURCE,
            self::$ACL_TIMEZONE_RESOURCE,
            self::$ACL_BBB_CONFIGS_RESOURCE
        );
    }

    public static function getAuthMode($authModeId = null) {
        $rAuthModes = array(
            self::$LOCAL_AUTH_MODE => IMDT_Util_Translate::_('Local'),
            self::$LDAP_AUTH_MODE => IMDT_Util_Translate::_('LDAP'),
            self::$PERSONA_AUTH_MODE => IMDT_Util_Translate::_('Persona')
        );

        if ($authModeId == null) {
            return $rAuthModes;
        } else {
            return $rAuthModes[$authModeId];
        }
    }

    public static function getMeetingRoomPrivacyPolicy($privacyPolicyId = null) {
        $rPrivacyPolicy = array(
            self::$PUBLIC_MEETING_ROOM => IMDT_Util_Translate::_('Public'),
            self::$ANY_LOGGED_USER_MEETING_ROOM => IMDT_Util_Translate::_('Any logged user'),
            self::$ONLY_INVITED_USERS_MEETING_ROOM => IMDT_Util_Translate::_('Only invited users')
        );

        if ($privacyPolicyId == null) {
            return $rPrivacyPolicy;
        } else {
            return $rPrivacyPolicy[$privacyPolicyId];
        }
    }

    public static function getAccessProfile($accessProfileId = null) {
        $rAccessProfile = array(
            self::$SYSTEM_ADMINISTRATOR_PROFILE => IMDT_Util_Translate::_('System Administrator'),
            self::$SYSTEM_SUPPORT_PROFILE => IMDT_Util_Translate::_('Support System'),
            self::$SYSTEM_PRIVILEGED_USER_PROFILE => IMDT_Util_Translate::_('Privileged User System'),
            self::$SYSTEM_USER_PROFILE => IMDT_Util_Translate::_('System User'),
            self::$NA_PROFILE => IMDT_Util_Translate::_('Inherited from Group')
        );

        if ($accessProfileId == null) {
            return $rAccessProfile;
        } else {
            if(!isset($rAccessProfile[$accessProfileId])) {
                throw new Exception ('Unknown access profile ID: ' . $accessProfileId);
            }
            return $rAccessProfile[$accessProfileId];
        }
    }

    public static function getMeetingRoomStatus($meetingRoomStatusId = null) {
        $rMeetingRoomStatus = array(
            self::$ROOM_OPENED => IMDT_Util_Translate::_('Started Rooms'),
            self::$ROOM_WAITING => IMDT_Util_Translate::_('Waiting Rooms'),
            self::$ROOM_CLOSED => IMDT_Util_Translate::_('Ended Rooms')
        );

        if ($meetingRoomStatusId == null) {
            return $rMeetingRoomStatus;
        } else {
            return $rMeetingRoomStatus[$meetingRoomStatusId];
        }
    }

}
