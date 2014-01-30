<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SVN Tutorial</title>
<script language="javascript" src="index_dropdowns.js"></script>
<LINK REL=StyleSheet HREF="index_style.css" TYPE="text/css" MEDIA=screen>
</head>
<body>
<div id="top_shadow4">
  <div id="top_shadow3">
    <div id="top_shadow2">
      <div id="top_shadow1">
        <div id="page_header_container">
          <div id="UAbanner_div"> <img src="/indexImages/UAbanner-grey.gif" /> </div>
          <div id="header_div">
            <div id="header_txt"><a style="color:#FFFFFF;" href="https://trinity.sunion.arizona.edu">Student Unions / Student Affairs&nbsp;&nbsp;&nbsp;Marketing Common Tools</a></div>
            <div id="menubar">
              <div id="menu" style="border-left:solid 2px; border-color:#FFFFFF; width:165px;"><span href="#" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu1, '165px')" onMouseout="delayhidemenu()" style="padding-left:42px;">Web Servers</span></div>
              <div id="menu" style="width:300px; border-left:solid 2px; border-color:#FFFFFF;"><span href="#" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu2, '300px')" onMouseout="delayhidemenu()" style="padding-left:29px; margin-left:2px;">Student Unions Working Copy Domains</span></div>
              <div id="menu" style="width:300px; border-left:solid 2px; border-right:solid 2px; border-color:#FFFFFF;"><span href="#" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu3, '300px')" onMouseout="delayhidemenu()" style="padding-left:29px; margin-left:2px;">Student Affairs Working Copy Domains</span></div>
            </div>
            <!--<div id="head_pic_div">
        </div>
        <div id="head_title_div">
        	<div id="head_title_img_div">
	        	<img src="/indexImages/studentaffairs.gif"  />
            </div>

        </div>-->
          </div>
          <!--<div id="welcome_header_div">
    	<img src="/banner.jpg" alt="Common Tools"/> 
    </div>-->
        </div>
      </div>
    </div>
  </div>
</div>
<div id="shadow1">
  <div id="shadow2">
    <div id="shadow3">
      <div id="shadow4">
        <div id="page_content_container" style="position:relative;">
          <h2 style="color:#22314e;margin-left:20px; margin-bottom:0; margin-right:0; margin-top:10px;">SVN Tutorial</h2>
          <div id="content" style="float:left; width:946px;">
            <div class="entry">
              <div class="snapshot"><img src="indexImages/snapshots/subversion_logo.png" /></div>
              <div class="entry_txt">
                <p>What is SVN?</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;SVN is a versioning tool which allows for control over document changes and a centralization of our code.</p>
              </div>
              <hr />
            </div>
            <div class="entry">
              <div class="entry_txt">
                <p>Where does it live?</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;The SVN Repositories are located on Cash and are accessed via https with the use of apache.</p>
              </div>
              <hr />
            </div>
            <div class="entry">
              <div class="entry_txt">
                <p>How do I access the repositories?</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;The SVN Repositories protected by your day-to-day user authentication data used on cash.</p>
              </div>
              <hr />
            </div>
            <div style="float:left;">
              <p style="font-weight:bold;color:#22314e;font-size:14px;margin-bottom:8px;margin-left:0px;">Checking out the Repository</p>
              <div class="topic">
                <div class="topic_txt">
                  <p>Using the commandline to checkout a working copy of the repositories data</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;To checkout a working copy of a given repository first mount the WebServer directory from the testing server (Elvis) over AFP. Then open a terminal shell and from there change directories with the "cd" command to the location of your user directory provided withing the staging server. Make a directory in this location if not already present in which you will checkout the working copy (use the "mkdir" command). Then commense the checkout using the "svn checkout" command as found in the picture below. This may promp you for a series of things such as needing to permanently accepting the RSA fingerprint of the server, your username, or your password. If you do not see a list of files begin to checkout or get an error please conact the appropriate server administrator.</p>
                  <img src="indeximages/svn/checkout.png" /> </div>
                <hr />
              </div>
            </div>
            <div style="float:left;">
              <p style="font-weight:bold;color:#22314e;font-size:14px;margin-bottom:8px;margin-left:0px;">Setting Up Dreamweaver</p>
              <div class="topic">
                <div class="topic_txt">
                  <p>Setting-up the Local Info</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;On this page of the site management set the site name to the something familair for the appropriate site. Then set the local root folder to the appropriate site directory under your username under svnstaging in the Elvis "WebServer" afp mount. This path should look something like "/Volumes/WebServer/svnstaging/USER/SITE" The http address should be the sites live address such as "union.arizona.edu".</p>
                  <img src="indeximages/svn/local.png" /> </div>
                <hr />
              </div>
              <div class="topic">
                <div class="topic_txt">
                  <p>Setting-up the Remote Info</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;On this page of the site management set the FTP host to trinity.sunion.arizona.edu. Then set the Host directory to / (this may also work blank). Then set the login to the appropriate live server user and its password. Make sure to also check the box that says "Use Secure FTP (SFTP)".</p>
                  <img src="indeximages/svn/remote.png" /> </div>
                <hr />
              </div>
              <div class="topic">
                <div class="topic_txt">
                  <p>Setting-up the Testing Server Info</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;On this page of the site management set the server model to "PHP MySQL" and the Access to "Local/Network" then set the testing server folder to the same path used as the local root folder path in the remote info. The URL Prefix should then be set to your subdomain on the staging server that is appropriate for the given site.</p>
                  <img src="indeximages/svn/testing.png" /> </div>
                <hr />
              </div>
              <div class="topic">
                <div class="topic_txt">
                  <p>Setting-up the Version Control Info</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;On this page of the site management set the Access to "Subverision" and then set the protocol to "HTTPS". Then set the server address to be cash, set the appropriate repository path, and your username and password used for day-to-day user authentication data used on cash. The SVN repository paths can be found on the main common tools page on the webserver at <a class="links" href="https://trinity.sunion.arizona.edu/index.php">https://trinity.sunion.arizoan.edu</a></p>
                  <img src="indeximages/svn/versioned.png" /> </div>
                <hr />
              </div>
            </div>
            <div style="float:left;">
              <p style="font-weight:bold;color:#22314e;font-size:14px;margin-bottom:8px;margin-left:0px;">Accessing Working Copies from Apache</p>
              <div class="topic">
                <div class="topic_txt">
                  <p>Configuring DNS translation by use of host table</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;To be able to access the working copies of our sites you must hit them at the designated URL of your first initial then last name the a dot and the abbreviation of the given site. You can already access these sub domains on the test server from our machines in the office as we have told your machine what address to resolve this too and how to pass it along to apache on elvis with the proper request. As seen bellow to be able to hit these domains from other machines the hosts table must be modified in your operating system. On a UNIX operating system this generally located in "/etc/hosts" we can add entries to this by using a text editor as the root user. The proper entry format is to put on the left a given a address then a space and the name which is to be resolved to that address. A list of these subdomains is found in the drop-down menu on the main common tools page on the webserver at <a class="links" href="https://trinity.sunion.arizona.edu/index.php">https://trinity.sunion.arizoan.edu</a></p>
                  <img src="indeximages/svn/hoststable.png" /> </div>
                <hr />
              </div>
            </div>
            <div style="float:left;">
              <p style="font-weight:bold;color:#22314e;font-size:14px;margin-bottom:8px;margin-left:0px;">Working in Dreamweaver</p>
              <div class="topic">
                <div class="topic_txt">
                  <p>Using Dreamweaver with SVN and our workflow</p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;This is still being figured out as we all know. Reading about SVN commands and Dreamweaver's help files provides some insight.</p>
                  <img src="indeximages/svn/panel.png" /> </div>
                <hr />
              </div>
            </div>
          </div>
          <div id="contact_info">
            <p> Student Union / Student Affiars  Marketing Common Tools<br />
              The University of Arizona<br />
              Student Student Union Memorial Center, Rm 441W<br />
              phone: 520.626.3128 <br />
              email: N/A </p>
          </div>
          <div style="clear: both;"/>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="footer_div"> </div>
</body>
</html>