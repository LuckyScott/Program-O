<?php
/***************************************
 * http://www.program-o.com
 * PROGRAM O
 * Version: 2.6.8
 * FILE: select_bots.php
 * AUTHOR: Elizabeth Perreau and Dave Morton
 * DATE: 12-09-2014
 * DETAILS: Selects the current chatbot and displays editable config data
 ***************************************/

$selectBot = '';
$curBot = array();
$post_vars = filter_input_array(INPUT_POST);

if ((isset($post_vars['action'])) && ($post_vars['action'] == "update"))
{
    $selectBot .= getChangeList();
    $msg = updateBotSelection();
    $selectBot .= getSelectedBot();
}
elseif ((isset($post_vars['action'])) && ($post_vars['action'] == "change")) {
    $bot_id = $post_vars['bot_id'];
    changeBot();
    $selectBot .= getChangeList();
    $selectBot .= getSelectedBot();
}
elseif ((isset($post_vars['action'])) && ($post_vars['action'] == "add"))
{
    $selectBot .= addBot();
    $selectBot .= getChangeList();
    $selectBot .= getSelectedBot();
}
else {
    $selectBot .= getChangeList();
    $selectBot .= getSelectedBot();
}
$bot_format = (isset($curBot['format'])) ? $curBot['format'] : $format;
$_SESSION['poadmin']['format'] = $bot_format;

$topNav         = $template->getSection('TopNav');
$leftNav        = $template->getSection('LeftNav');
$main           = $template->getSection('Main');
$navHeader      = $template->getSection('NavHeader');
$FooterInfo     = getFooter();
$errMsgClass    = (!empty($msg)) ? "ShowError" : "HideError";
$errMsgStyle    = $template->getSection($errMsgClass);
$noLeftNav      = '';
$noTopNav       = '';
$noRightNav     = $template->getSection('NoRightNav');
$headerTitle    = 'Actions:';
$pageTitle      = 'My-Program O - Select or Edit a Bot';
$mainContent    = $selectBot;
$mainTitle      = 'Choose/Edit a Bot';

/**
 * Returns a list of current, active chatbots, for selecting a parent chatbot
 *
 * @param $current_parent
 * @return string
 */
function getBotParentList($current_parent)
{

    //get active bots from the db
    if (empty($current_parent))
    {
        $current_parent = 0;
    }

    /** @noinspection SqlDialectInspection */
    $sql = "SELECT * FROM bots WHERE bot_active = '1'";
    $result = db_fetchAll($sql,null, __FILE__, __FUNCTION__, __LINE__);
    $options = '                  <option value="0"[noBot]>No Parent Bot</option>';

    foreach ($result as $row)
    {
        if ($row['bot_id'] == 0)
        {
            $options = str_replace('[noBot]', 'selected="selected"', $options);
        }

        if ($current_parent == $row['bot_id'])
        {
            $sel = "selected=\"selected\"";
        }
        else {
            $sel = '';
        }

        $options .= '                  <option value="' . $row['bot_id'] . '" ' . $sel . '>' . $row['bot_name'] . '</option>';
    }
    $options = str_replace('[noBot]', 'selected="selected"', $options);

    return $options;
}


/**
 * Returns an HTML form, filled with the current chatbot's configuration data
 *
 * @return string
 */
function getSelectedBot()
{
    global $template, $pattern, $remember_up_to, $conversation_lines, $error_response, $curBot, $unknown_user;
    $bot_conversation_lines = $conversation_lines;
    $bot_default_aiml_pattern = $pattern;
    $bot_error_response = $error_response;
    $bot_unknown_user = $unknown_user;

    $inputs = '';
    $aiml_count = 'no';
    $form = $template->getSection('SelectBotForm');
    $sel_session = '';
    $sel_db = '';
    $sel_html = '';
    $sel_xml = '';
    $sel_json = '';
    $sel_yes = '';
    $sel_no = '';
    $sel_fyes = '';
    $sel_fno = '';
    $sel_fuyes = '';
    $sel_funo = '';
    $ds_ = '';
    $ds_i = '';
    $ds_ii = '';
    $ds_iii = '';
    $ds_iv = '';
    $dm_ = '';
    $dm_i = '';
    $dm_ii = '';
    $dm_iii = '';
    $dm_iv = '';

    $bot_id = (isset($_SESSION['poadmin']['bot_id'])) ? $_SESSION['poadmin']['bot_id'] : 'new';

    if ($bot_id != "new")
    {
        /** @noinspection SqlDialectInspection */
        $sql = "SELECT count(*) FROM aiml WHERE bot_id = :bot_id;";
        $row = db_fetch($sql, array(':bot_id' => $bot_id), __FILE__, __FUNCTION__, __LINE__);
        $aiml_count = ($row['count(*)'] == 0) ? 'no' : number_format($row['count(*)']);

        /** @noinspection SqlDialectInspection */
        $sql = "SELECT * FROM bots WHERE bot_id = :bot_id;";
        $row = db_fetch($sql, array(':bot_id' => $bot_id), __FILE__, __FUNCTION__, __LINE__);
        $curBot = $row;

        foreach ($row as $key => $value)
        {
            if (strstr($key, 'bot_') != false)
            {
                $tmp = '';
                $$key = $value;
            }
            else
            {
                $tmp = "bot_$key";
                $$tmp = $value;
            }
        }

        if ($bot_active == "1") {
            $sel_yes = ' selected="selected"';
        } else {
            $sel_no = ' selected="selected"';
        }

        if ($bot_save_state == "database") {
            $sel_db = ' selected="selected"';
        } else {
            $sel_session = ' selected="selected"';
        }

        if ($bot_format == "html") {
            $sel_html = ' selected="selected"';
        } elseif ($bot_format == "xml") {
            $sel_xml = ' selected="selected"';
        } elseif ($bot_format == "json") {
            $sel_json = ' selected="selected"';
        }

        if ($bot_debugshow == "0") {
            $ds_ = ' selected="selected"';
        } elseif ($bot_debugshow == "1") {
            $ds_i = ' selected="selected"';
        } elseif ($bot_debugshow == "2") {
            $ds_ii = ' selected="selected"';
        } elseif ($bot_debugshow == "3") {
            $ds_iii = ' selected="selected"';
        } elseif ($bot_debugshow == "4") {
            $ds_iv = ' selected="selected"';
        }

        /** @noinspection PhpUndefinedVariableInspection */
        if ($bot_debugmode == "0") {
            $dm_ = ' selected="selected"';
        } elseif ($bot_debugmode == "1") {
            $dm_i = ' selected="selected"';
        } elseif ($bot_debugmode == "2") {
            $dm_ii = ' selected="selected"';
        } elseif ($bot_debugmode == "3") {
            $dm_iii = ' selected="selected"';
        } elseif ($bot_debugmode == "4") {
            $dm_iv = ' selected="selected"';
        }

        $action = "update";
    }
    else
    {
        $bot_id = '';
        $bot_parent_id = 0;
        $bot_name = 'new or unnamed bot';
        $bot_desc = '';
        $bot_active = '';
        $action = "add";
        $bot_format = '';
        $bot_conversation_lines = $conversation_lines;
        //$remember_up_to = $remember_up_to;
        $bot_default_aiml_pattern = $pattern;
        $bot_error_response = $error_response;
        $bot_debugemail = '';
        $debugemail = '';
        $bot_debugshow = '';
        $bot_debugmode = '';
    }

    $parent_options = getBotParentList($bot_parent_id);
    $searches = array(
        '[bot_id]', '[bot_name]', '[aiml_count]', '[bot_desc]', '[parent_options]', '[sel_yes]', '[sel_no]',
        '[sel_html]', '[sel_xml]', '[sel_json]', '[sel_session]', '[sel_db]', '[sel_fyes]',
        '[sel_fno]', '[sel_fuyes]', '[sel_funo]', '[bot_conversation_lines]', '[remember_up_to]',
        '[bot_debugemail]', '[dm_]', '[dm_i]', '[dm_ii]', '[dm_iii]', '[ds_]', '[ds_i]', '[ds_ii]',
        '[ds_iii]', '[ds_iv]', '[action]', '[bot_default_aiml_pattern]', '[bot_error_response]', '[bot_unknown_user]', '[unknown_user]',
    );

    foreach ($searches as $search)
    {
        $replace = str_replace('[', '', $search);
        $replace = str_replace(']', '', $replace);
        $form = str_replace($search, $$replace, $form);
    }

    return $form;
}

/**
 * Updates the database whth the current chatbot's modified configuration data
 *
 * @return string
 */
function updateBotSelection()
{
    global $msg, $format, $post_vars, $branch;

    $logFile = _LOG_URL_ . 'admin.error.log';
    $msg = '';
    $bot_id = $post_vars['bot_id'];
    /** @noinspection SqlDialectInspection */
    $sql = "SELECT * FROM bots WHERE bot_id = :bot_id;";
    $params = array(':bot_id' => $bot_id);
    $result = db_fetch($sql, $params, __FILE__, __FUNCTION__, __LINE__);
    $sql = 'UPDATE bots [repl] WHERE bot_id = :bot_id;';
    $skipVars = array('bot_id', 'action', 'useBranch');
    $setTemplate = ' set [key] = :[key],';
    $repl = '';
    foreach ($post_vars as $key => $value)
    {
        if (in_array($key, $skipVars) || !isset($result[$key])) {
            continue;
        }

        if ($result[$key] != $post_vars[$key] && !in_array($key, $skipVars))
        {
            $newSet = $setTemplate;
            $newSet = str_replace('[key]', $key, $newSet);
            $repl .= $newSet;
            $params[":{$key}"] = $value;
        }
    }

    if (!empty($repl))
    {
        $repl = rtrim($repl, ',');
        $sql = str_replace('[repl]', $repl, $sql);
        save_file(_LOG_PATH_ . 'select_bots.update.params.txt', print_r($params, true));
        save_file(_LOG_PATH_ . 'select_bots.update.sql.txt', print_r($sql, true));
        $affectedRows = db_write($sql, $params, false, __FILE__, __FUNCTION__, __LINE__);

        if ($affectedRows == 0)
        {
            $msg = "Error updating bot details. See the <a href=\"$logFile\">error log</a> for details.<br />";
            trigger_error("There was a problem adding '$key' to the database. The value was '$value'.");
            //return $msg;
        }
    }
    elseif ($branch != $post_vars['useBranch'])
    {
        $branch = $post_vars['useBranch'];
        $_SESSION['useBranch'] = $post_vars['useBranch'];
        unset($_SESSION['GitHubVersion']);
    }
    else {
        $msg = 'Nothing seems to have been modified. No changes made.';
    }
    $curFormat = _strtolower($_SESSION['poadmin']['format']);
    $format = _strtolower(filter_input(INPUT_POST, 'format'));

    if ($format !== $curFormat)
    {
        $_SESSION['poadmin']['format'] = $format;
        $cfn = _CONF_PATH_ . 'global_config.php';
        $configFileContent = file_get_contents(_CONF_PATH_ . 'global_config.php', FILE_IGNORE_NEW_LINES);
        $search = "/format = '.*?';/";
        $replace = "format = '{$format}';";
        $test = preg_match($search, $configFileContent, $matches);
        $configFileContent = preg_replace($search, $replace, $configFileContent);
        $x = file_put_contents(_CONF_PATH_ . 'global_config.php', $configFileContent);

        if (false === $x)
        {
            $msg .= "Error updating the config file. See the <a href=\"$logFile\">error log</a> for details.<br />";
            trigger_error("There was a problem with updating the default format in the config file. Please edit the value manually and submit a bug report.");
        }
    }

    if ($msg == '') {
        $msg = 'Bot details updated.';
    }

    return $msg;
}


/**
 * Adds a new chatbot to the database
 *
 * @return string
 */
function addBot()
{
    //db globals
    global $msg, $post_vars;

    foreach ($post_vars as $key => $value) {
        $$key = trim($value);
    }

    /** @noinspection PhpUndefinedVariableInspection */
    /** @noinspection SqlDialectInspection */
    $sql = 'INSERT INTO bots(bot_name, bot_desc, bot_active, bot_parent_id, format, save_state, conversation_lines, remember_up_to, debugemail, debugshow, debugmode, default_aiml_pattern, error_response)
VALUES (:bot_name,:bot_desc,:bot_active,:bot_parent_id,:format,:save_state,:conversation_lines,:remember_up_to,:debugemail,:debugshow,:debugmode,:aiml_pattern,:error_response);';
    $params = array(
        ':bot_name'             => $bot_name,
        ':bot_desc'             => $bot_desc,
        ':bot_active'           => $bot_active,
        ':bot_parent_id'        => $bot_parent_id,
        ':format'               => $format,
        ':save_state'           => $save_state,
        ':conversation_lines'   => $conversation_lines,
        ':remember_up_to'       => $remember_up_to,
        ':debugemail'           => $debugemail,
        ':debugshow'            => $debugshow,
        ':debugmode'            => $debugmode,
        ':aiml_pattern'         => $default_aiml_pattern,
        ':error_response'       => $error_response
    );
    $affectedRows = db_write($sql, $params, false, __FILE__, __FUNCTION__, __LINE__);

    if ($affectedRows > 0)
    {
        $msg = "$bot_name Bot details added, please dont forget to create the bot personality and add the aiml.";

    }
    else {
        $msg = "$bot_name Bot details could not be added.";
    }

    $_SESSION['poadmin']['bot_id'] = db_lastInsertId();
    $bot_id = $_SESSION['poadmin']['bot_id'];
    $_SESSION['poadmin']['bot_name'] = $post_vars['bot_name'];
    $bot_name = $_SESSION['poadmin']['bot_name'];
    $msg .= make_bot_predicates($bot_id);

    return $msg;
}

/**
 * Adds default predicate (personality) data to the database for the current chatbot
 *
 * @param $bot_id
 * @return string
 */
function make_bot_predicates($bot_id)
{
    global $bot_name;
    $msg = '';

    $sql = <<<endSQL
INSERT INTO botpersonality (bot_id, name, value) VALUES
    (:bot_id, 'age', ''),
    (:bot_id, 'baseballteam', ''),
    (:bot_id, 'birthday', ''),
    (:bot_id, 'birthplace', ''),
    (:bot_id, 'botmaster', ''),
    (:bot_id, 'boyfriend', ''),
    (:bot_id, 'build', ''),
    (:bot_id, 'celebrities', ''),
    (:bot_id, 'celebrity', ''),
    (:bot_id, 'class', ''),
    (:bot_id, 'email', ''),
    (:bot_id, 'emotions', ''),
    (:bot_id, 'ethics', ''),
    (:bot_id, 'etype', ''),
    (:bot_id, 'family', ''),
    (:bot_id, 'favoriteactor', ''),
    (:bot_id, 'favoriteactress', ''),
    (:bot_id, 'favoriteartist', ''),
    (:bot_id, 'favoriteauthor', ''),
    (:bot_id, 'favoriteband', ''),
    (:bot_id, 'favoritebook', ''),
    (:bot_id, 'favoritecolor', ''),
    (:bot_id, 'favoritefood', ''),
    (:bot_id, 'favoritemovie', ''),
    (:bot_id, 'favoritesong', ''),
    (:bot_id, 'favoritesport', ''),
    (:bot_id, 'feelings', ''),
    (:bot_id, 'footballteam', ''),
    (:bot_id, 'forfun', ''),
    (:bot_id, 'friend', ''),
    (:bot_id, 'friends', ''),
    (:bot_id, 'gender', ''),
    (:bot_id, 'genus', ''),
    (:bot_id, 'girlfriend', ''),
    (:bot_id, 'hockeyteam', ''),
    (:bot_id, 'kindmusic', ''),
    (:bot_id, 'kingdom', ''),
    (:bot_id, 'language', ''),
    (:bot_id, 'location', ''),
    (:bot_id, 'looklike', ''),
    (:bot_id, 'master', ''),
    (:bot_id, 'msagent', ''),
    (:bot_id, 'name', :bot_name),
    (:bot_id, 'nationality', ''),
    (:bot_id, 'order', ''),
    (:bot_id, 'orientation', ''),
    (:bot_id, 'party', ''),
    (:bot_id, 'phylum', ''),
    (:bot_id, 'president', ''),
    (:bot_id, 'question', ''),
    (:bot_id, 'religion', ''),
    (:bot_id, 'sign', ''),
    (:bot_id, 'size', ''),
    (:bot_id, 'species', ''),
    (:bot_id, 'talkabout', ''),
    (:bot_id, 'version', ''),
    (:bot_id, 'vocabulary', ''),
    (:bot_id, 'wear', ''),
    (:bot_id, 'website', '');
endSQL;
    $params = array(':bot_id' => $bot_id, ':bot_name' => $bot_name);

    $affectedRows = db_write($sql, $params, false, __FILE__, __FUNCTION__, __LINE__);

    if ($affectedRows > 0)
    {
        $msg .= 'Please create the bots personality.';
    }
    else {
        $msg .= 'Unable to create the bots personality.';
    }

    return $msg;
}

/**
 * Changes the current chatbot
 *
 * @return void
 */
function changeBot()
{
    global $msg, $bot_id, $post_vars, $branch;
    $botId = (isset($post_vars['bot_id'])) ? $post_vars['bot_id'] : $bot_id;

    if ($post_vars['bot_id'] != "new")
    {
        /** @noinspection SqlDialectInspection */
        $sql = "SELECT * FROM bots WHERE bot_id = :botId";
        $params = array(':botId' => $botId);
        $row = db_fetch($sql, $params, __FILE__, __FUNCTION__, __LINE__);
        $count = count($row);

        if ($count > 0)
        {
            $_SESSION['poadmin']['format'] = $row['format'];
            $_SESSION['poadmin']['bot_id'] = $row['bot_id'];
            $_SESSION['poadmin']['bot_name'] = $row['bot_name'];
        }
        else
        {
            $_SESSION['poadmin']['bot_id'] = "new";
            $_SESSION['poadmin']['bot_name'] = '<b class="red">unnamed bot</b>';
        }
    }
    else
    {
        $_SESSION['poadmin']['bot_name'] = '<b class="red">unnamed bot</b>';
        $_SESSION['poadmin']['bot_id'] = "new";
    }

    header("Location: index.php?page=select_bots");
}


/**
 * Returns an HTML form for selecting a chatbot from the database
 *
 * @return string
 */
function getChangeList()
{
    global $template;
    $bot_id = (isset($_SESSION['poadmin']['bot_id'])) ? $_SESSION['poadmin']['bot_id'] : 0;

    /** @noinspection SqlDialectInspection */
    $sql = "SELECT * FROM bots ORDER BY bot_name";
    $result = db_fetchAll($sql,null, __FILE__, __FUNCTION__, __LINE__);
    $options = '                <option value="new">Add New Bot</option>' . "\n";

    foreach ($result as $row)
    {
        $options .= "<!-- bot ID = {$row['bot_id']}, {$bot_id} -->\n";

        if ($bot_id == $row['bot_id'])
        {
            $sel = ' selected="selected"';
        }
        else {
            $sel = '';
        }

        $bot_id = $row['bot_id'];
        $bot_name = $row['bot_name'];
        $options .= "                <option value=\"$bot_id\"$sel>$bot_name</option>\n";
    }

    $options = rtrim($options);
    $form = $template->getSection('ChangeBot');
    $form = str_replace('[options]', $options, $form);

    return $form;
}
