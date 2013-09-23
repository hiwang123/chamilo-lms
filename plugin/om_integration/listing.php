<?php
/**
 * This script initiates a videoconference session, calling the BigBlueButton API
 * @package chamilo.plugin.bigbluebutton
 */
/**
 * Initialization
 */

$course_plugin = 'om_integration'; //needed in order to load the plugin lang variables
require_once dirname(__FILE__).'/config.php';
$plugin = om_integrationPlugin::create();
$tool_name = $plugin->get_lang('Videoconference');
$tpl = new Template($tool_name);

$om = new om_integration();
$action = isset($_GET['action']) ? $_GET['action'] : null;

$teacher = $om->is_teacher();

api_protect_course_script(true);
$message = null;

if ($teacher) {
    switch ($action) {
        case 'add_to_calendar':
            $course_info = api_get_course_info();
            $agenda = new Agenda();
            $agenda->type = 'course';

            $id = intval($_GET['id']);
            $title = sprintf(get_lang('VideoConferenceXCourseX'), $id, $course_info['name']);
            $content = Display::url(get_lang('GoToTheVideoConference'), $_GET['url']);

            $event_id = $agenda->add_event($_REQUEST['start'], null, 'true', null, $title, $content, array('everyone'));
            if (!empty($event_id)) {
                $message = Display::return_message(get_lang('VideoConferenceAddedToTheCalendar'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'copy_record_to_link_tool':
            $result = $om->copy_record_to_link_tool($_GET['id'], $_GET['record_id']);
            if ($result) {
                $message = Display::return_message(get_lang('VideoConferenceAddedToTheLinkTool'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'delete_record':
            $om->delete_record($_GET['id']);
            if ($result) {
                $message = Display::return_message(get_lang('Deleted'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'end':
            $om->end_meeting($_GET['id']);
            $message = Display::return_message(get_lang('MeetingClosed').'<br />'.get_lang('MeetingClosedComment'), 'success', false);
            break;
        case 'publish':
            //$result = $om->publish_meeting($_GET['id']);
            break;
        case 'unpublish':
            //$result = $om->unpublish_meeting($_GET['id']);
            break;
        default:
            break;
    }
}

$meetings = $om->get_course_meetings();
if (!empty($meetings))  $meetings = array_reverse($meetings);

$users_online = $meetings->participantCount;
$status = !$meetings->isClosed;
$meeting_exists = 1;//$om->meeting_exists(api_get_course_id());
$show_join_button = false;
if ($meeting_exists || $teacher) {
    $show_join_button = true;
}

$tpl->assign('allow_to_edit', $teacher);
$tpl->assign('meetings', $meetings);
$conference_url = api_get_path(WEB_PLUGIN_PATH).'om_integration/start.php?launch=1&'.api_get_cidreq();
$tpl->assign('conference_url', $conference_url);
$tpl->assign('users_online', $users_online);
$tpl->assign('bbb_status', $status);
$tpl->assign('show_join_button', $show_join_button);

//$tpl->assign('actions', $actions);
$tpl->assign('message', $message);
$listing_tpl = 'om_integration/listing.tpl';
$content = $tpl->fetch($listing_tpl);
$tpl->assign('content', $content);$tpl->display_one_col_template();
