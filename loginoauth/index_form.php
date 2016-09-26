<?php

$thinkphpurl = $_SERVER["QUERY_STRING"];

//require_once 'oauth2/zwoauth.class.php';
//$oauth = new ZWPDOOAuth2();

$auth_params = $oauth->getAuthorizeParams();

$resultClass = $oauth->finishClientAuthorization(true, $_GET);
$code = $resultClass->result['query']['code'];

if ($show_instructions) {
    $columns = 'twocolumns';
} else {
    $columns = 'onecolumn';
}

if (!empty($CFG->loginpasswordautocomplete)) {
    $autocomplete = 'autocomplete="off"';
} else {
    $autocomplete = '';
}
if (empty($CFG->authloginviaemail)) {
    $strusername = get_string('username');
} else {
    $strusername = get_string('usernameemail');
}
?>
<div class="loginbox clearfix <?php echo $columns ?>">
  <div class="loginpanel">
<?php
  if (($CFG->registerauth == 'email') || !empty($CFG->registerauth)) { ?>
      <div class="skiplinks"><a class="skip" href="signup.php"><?php print_string("tocreatenewaccount"); ?></a></div>
<?php
  } ?>
<!--    <h2>--><?php //print_string("login") ?><!--</h2>-->
    <h1>&nbsp;&nbsp;绩效管理系统登录</h1>
      <div class="login" style="margin-top:50px;">
        <?php
          if (!empty($errormsg)) {
              echo html_writer::start_tag('div', array('class' => 'loginerrors'));
              echo html_writer::link('#', $errormsg, array('id' => 'loginerrormessage', 'class' => 'accesshide'));
              echo $OUTPUT->error_text($errormsg);
              echo html_writer::end_tag('div');
          }
        ?>

          <div class="header">
              <div class="switch" id="switch">
                  <a class="switch_btn_focus">快速登录</a>
              </div>
          </div>
          <div class="web_qr_login">
              <!--登录-->
              <div class="web_login" id="web_login">
                  <div class="login-box">
                      <div class="login_form">
                          <form action="<?php echo $CFG->httpswwwroot; ?>/loginoauth/index.php" method="post" id="login" <?php echo $autocomplete; ?> >
                              <?php foreach ($auth_params as $k => $v) { ?>
                                  <input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
                              <?php } ?>

                              <input type="hidden" name="code" value="<?php echo $code;?>" id="code">
                              <input type="hidden" name="thinkphpurl" value="<?php echo $thinkphpurl;?>" id="thinkphpurl">
                              <input type="hidden" name="grant_type" value="authorization_code" id="grant_type">

                              <div class="uinArea" id="uinArea">
                                  <div class="inputOuter" id="uArea">
                                      <input type="text" name="username" id="u" class="inputstyle" placeholder="<?php echo($strusername) ?>" autocomplete="off" size="15" value="<?php p($frm->username) ?>" />
                                  </div>
                              </div>
                              <div class="pwdArea" id="pwdArea">
                                  <div class="inputOuter" id="pArea">
                                      <input type="password" name="password" id="p" class="inputstyle" placeholder="<?php print_string("password") ?>" oncontextmenu="return false" onpaste="return false" <?php echo $autocomplete; ?> />
                                  </div>
                              </div>

                              <div style="margin-top:20px;"><input type="submit" value="登 录" style="width:100%;" class="button_blue"/></div>

                          </form>
<!--        <div class="desc" style="margin:20px 20px 0px 0px"><!--加了style-->
<!---->
<!--        </div>-->
                      </div>
                  </div>
              </div>
              <!--登录end-->
          </div>
      </div>

<?php if ($show_instructions) { ?>
    <div class="signuppanel">
      <h2><?php print_string("firsttime") ?></h2>
      <div class="subcontent">
<?php     if (is_enabled_auth('none')) { // instructions override the rest for security reasons
              print_string("loginstepsnone");
          } else if ($CFG->registerauth == 'email') {
              if (!empty($CFG->auth_instructions)) {
                  echo format_text($CFG->auth_instructions);
              } else {
                  print_string("loginsteps", "", "signup.php");
              } ?>
                 <div class="signupform">
                   <form action="signup.php" method="get" id="signup">
                   <div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
                   </form>
                 </div>
<?php     } else if (!empty($CFG->registerauth)) {
              echo format_text($CFG->auth_instructions); ?>
              <div class="signupform">
                <form action="signup.php" method="get" id="signup">
                <div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
                </form>
              </div>
<?php     } else {
              echo format_text($CFG->auth_instructions);
          } ?>
      </div>
    </div>
<?php } ?>
<?php if (!empty($potentialidps)) { ?>
    <div class="subcontent potentialidps">
        <h6><?php print_string('potentialidps', 'auth'); ?></h6>
        <div class="potentialidplist">
<?php foreach ($potentialidps as $idp) {
    echo  '<div class="potentialidp"><a href="' . $idp['url']->out() . '" title="' . $idp['name'] . '">' . $OUTPUT->render($idp['icon'], $idp['name']) . $idp['name'] . '</a></div>';
} ?>
        </div>
    </div>
<?php } ?>
</div>
