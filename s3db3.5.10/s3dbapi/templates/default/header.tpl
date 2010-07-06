<!-- BEGIN head -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<META name="AUTHOR" content="S3DB http://bioinformatics.musc.edu/s3db">
<META NAME="description" CONTENT="S3DB">
<META NAME="keywords" CONTENT="S3DB">
<meta name="robots" content="none">
<LINK REL="ICON" href="{img_icon}" type="image/x-ico">
<LINK REL="SHORTCUT ICON" href="{img_shortcut}">

<script language="javascript" src="{uri_base}/js/m-x.js"></script>
<script language="javascript" src="{uri_base}/js/DcomboBox.js"></script>
<script language="javascript" src="{uri_base}/js/autocomplete.js"></script>
<script language="javascript" src="{uri_base}/js/shownhidden.js"></script>
<script language="javascript" src="{uri_base}/js/tooltip.js"></script>

<script type="text/Javascript">





function shownhidden (id)
{
        details = document.getElementById(id);
        if (details.className=="shown")
        {
                details.className="hidden";
        }
        else
        {
                details.className="shown";
        }
}
var checkflag = "false";



function check() 
{
	if(checkflag == "false")
	{
		for(j = 0; j < document.queryresource.elements.length; j++) 
		{
			if(document.queryresource.elements[j].name == "show_me[]")
			{
				document.queryresource.elements[j].checked = true;
			}
		}
		checkflag = "true";
		return "Uncheck All";
	}
	else
	{
		for(j = 0; j < document.queryresource.elements.length; j++) 
		{
			if(document.queryresource.elements[j].name == "show_me[]")
			{
				document.queryresource.elements[j].checked = false; 
			}
		}
		checkflag = "false";
		return "Check All";

	}
}
function set_num_per_page(value)
{
	document.write('<input type="hidden" name="numperpage" value="'+value+'">');
}
</script>
<TITLE>{website_title}</TITLE>
<!-- END head -->
<!__ "CONVERTED_APPLET"-->
<!-- CONVERTER VERSION 1.3 -->
<SCRIPT LANGUAGE="JavaScript"><!--
    var _info = navigator.userAgent; var _ns = false;
    var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
//--></SCRIPT>
<COMMENT><SCRIPT LANGUAGE="JavaScript1.1"><!--
    var _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 && java.lang.System.getProperty("os.version").indexOf("3.5") < 0) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0)));
//--></SCRIPT></COMMENT>

<SCRIPT LANGUAGE="JavaScript"><!--
    if (_ie == true) document.writeln('<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = 100% HEIGHT = 100%  codebase="http://java.sun.com/products/plugin/1.3/jinstall-13-win32.cab#Version=1,3,0,0"><NOEMBED><XMP>');
    else if (_ns == true) document.writeln('<EMBED type="application/x-java-applet;version=1.3"  CODE = "com.touchgraph.linkbrowser.LinkBrowserApplet.class" ARCHIVE = "TGLinkBrowser.jar, nanoxml-2.1.1.jar, BrowserLauncher.jar" initialXmlFile = "InitialXML.xml" WIDTH = 100% HEIGHT = 100% browser = "yes"  scriptable=false pluginspage="http://java.sun.com/products/plugin/1.3/plugin-install.html"><NOEMBED><XMP>');
//--></SCRIPT>

<!-- BEGIN menu -->
<style type="text/css" media="screen">

<!-- Lenas -->
p {
  background-color: lightyellow;
  font-family: "comic sans ms";
  font-size: 13;
  color: dodgerblue;
  }
 
 p1 {
  background-color: lightblue;
  font-family: "comic sans ms";
  font-size: 13;
  color: dodgerblue;
  }

<!-- End Lena\'s -->

body {
	margin : 0px;
	font: Verdana, Helvetica, Arial;
	padding: 0px;
	background: #fff;
}

#menu {
	border-bottom : 1px solid dodgerblue;
	margin : 0;
	padding-bottom : 19px;
	padding-left : 10px;
}

#menu ul, #menu li	{
	display : inline;
	list-style-type : none;
	margin : 0;
	padding : 0;
}

	
#menu a:link, #menu a:visited	{
	/*background : #E8EBF0; */
	background-image: url('{uri_base}/images/gradient_thead.gif');
	/*background : dodgerblue;*/
	border : 1px solid dodgerblue;
	/*color : #666;*/
	color : white;
	float : left;
	font-size : smaller;
	font-weight : bold;
	line-height : 14px;
	margin-right : 8px;
	padding : 2px 10px 2px 10px;
	text-decoration : none;
}

#menu a:link.active, #menu a:visited.active	{
	background : #fff;
	border-bottom : 1px solid #fff;
	color : navy;
}

#menu a:hover	{
	/*color : #f00;*/
	color : yellow;
}

	
body.section-1 #menu li#nav-1 a, 
body.section-2 #menu li#nav-2 a,
body.section-3 #menu li#nav-3 a,
body.section-4 #menu li#nav-4 a,
body.section-5 #menu li#nav-5 a,
body.section-6 #menu li#nav-6 a,
body.section-7 #menu li#nav-7 a {
	background : #fff;
	border-bottom : 1px solid #fff;
	color : navy;
}

table.section-1 #menu li#nav-1 a, 
table.section-2 #menu li#nav-2 a,
table.section-3 #menu li#nav-3 a,
table.section-4 #menu li#nav-4 a,
table.section-5 #menu li#nav-5 a,
table.section-6 #menu li#nav-6 a,
table.section-7 #menu li#nav-7 a {
	background : #fff;
	border-bottom : 1px solid #fff;
	color : navy;
}

#menu #subnav-1,
#menu #subnav-2,
#menu #subnav-3,
#menu #subnav-4,
#menu #subnav-5,
#menu #subnav-6,
#menu #subnav-7 {
	display : none;
	width: 90%;
}

body.section-1 #menu ul#subnav-1, 
body.section-2 #menu ul#subnav-2,
body.section-3 #menu ul#subnav-3,
body.section-4 #menu ul#subnav-4,
body.section-5 #menu ul#subnav-5,
body.section-6 #menu ul#subnav-6, 
body.section-7 #menu ul#subnav-7 {
	display : inline;
	left : 0px;
	position : absolute;
	top : 125px;
}

table.section-1 #menu ul#subnav-1, 
table.section-2 #menu ul#subnav-2,
table.section-3 #menu ul#subnav-3,
table.section-4 #menu ul#subnav-4,
table.section-5 #menu ul#subnav-5,
table.section-6 #menu ul#subnav-6, 
table.section-7 #menu ul#subnav-7 {
	display : inline;
	left : 0px;
	position : absolute;
	top : 125px;
}

body.section-1 #menu ul#subnav-1 a, 
body.section-2 #menu ul#subnav-2 a,
body.section-3 #menu ul#subnav-3 a,
body.section-4 #menu ul#subnav-4 a,
body.section-5 #menu ul#subnav-5 a,
body.section-6 #menu ul#subnav-6 a,
body.section-7 #menu ul#subnav-7 a {
	background : #fff;
	border : none;
	border-right : 1px solid dodgerblue;
	/*color : #999; */
	color : dodgerlblue;
	font-size : smaller;
	font-weight : bold;
	line-height : 10px;
	margin-right : 4px;
	padding : 2px 10px 2px 10px;
	text-decoration : none;
}

table.section-1 #menu ul#subnav-1 a, 
table.section-2 #menu ul#subnav-2 a,
table.section-3 #menu ul#subnav-3 a,
table.section-4 #menu ul#subnav-4 a,
table.section-5 #menu ul#subnav-5 a,
table.section-6 #menu ul#subnav-6 a,
table.section-7 #menu ul#subnav-7 a {
	background : #fff;
	border : none;
	border-right : 1px solid dodgerblue;
	/*color : #999; */
	color : dodgerlblue;
	font-size : smaller;
	font-weight : bold;
	line-height : 10px;
	margin-right : 4px;
	padding : 2px 10px 2px 10px;
	text-decoration : none;
}


 #menu ul a:hover {
	color : #f00 !important;
}

#contents {
	background : #fff;
	border : 1px solid dodgerblue;
	border-top : none;
	border-left : none;
	border-right : none;
	color: navy;
	clear : both;
	margin : 0px;
	padding : 15px;
}
td.message {
	color : red;
}
table.contents {
	background : #fff;
	border : 1px solid dodgerblue;
/*	border-bottom : none; */
	border-top : none;
	border-left : none;
	border-right : none;
	color: navy;
	clear : both;
	margin : 0px;
	padding : 15px;
	width : 100%;
}

table.insidecontents {
	color: navy;
}
table.resource_list {
	font-size: smaller;	
	color: navy;
	/*border : 1px solid dodgerblue;*/
	padding : 15px;
	width : 100%;
}
table.query_resource {
	color: navy;
	width : 100%;
	padding : 0px;
}
table.create_resource {
	color: navy;
	width : 100%;
	padding : 0px;
}
table.edit_rule {
	font-size: 90%;	
	color: navy;
	width : 100%;
	padding : 0px;
}
tr.odd {
	font-size: smaller;	
	background : #DDF0FF;
}
tr.even {
	font-size: smaller;	
	background : #CCEEFF;
}
tr.entry {
	font-size: smaller;	
}
tr.info {
	font-size: smaller;	
	width : 25%;
	text-align : left;
}
table.top {
	background : #fff;
	border : 1px solid dodgerblue;
	border-top : none;
	border-bottom : none;
	border-left : none;
	border-right : none;
	color: navy;
	clear : both;
	margin : 0px;
	padding : 15px;	
	width : 100%;
}
sup.required{
	color: red;
	
}

h1 {
	color: red;
}	
table.middle {
	background : #fff;
	border : 1px solid dodgerblue;
	border-top : none;
	border-bottom : none;
	border-left : none;
	border-right : none;
	color: navy;
	font-size: smaller
	clear : both;
	margin : 0px;
	padding : 0px;
	text-align : center;
	width : 100%;
}

table.bottom {
	font-size: smaller;	
	background : #fff;
	border : 1px solid dodgerblue;
	border-top : none;
	border-left : none;
	border-right : none;
	color: navy;
	clear : both;
	margin : 0px;
	padding : 15px;
}
table.login {	
	font-size: smaller;
	color: navy;
	text-decoration : none;
}
table.acl {	
	font-size: smaller;
	color: navy;
	text-decoration : none;
}
table.acl.td {	
	font-size: smaller;
	border : 1px solid dodgerblue;
	color: navy;
	text-decoration : none;
}
table.head {	
	font-size: smaller;
	color: navy;
	text-decoration : none;
	width : 100%;
}
table.datagrid {	
	font-size: smaller;
	font-weight : normal;
	color: navy; 
	text-decoration : none;
}
table.footer {	
	font-size: smaller;
	font-weight : bold;
	/*border : 1px solid dodgerblue;*/
	color: white; 
	background : royalblue;
	text-decoration : none;
}
table.footer a {	
	color: yellow;
	text-decoration : none;
}
table.menu {
	margin : 10px;
	font: Verdana, Helvetica, Arial;
	padding: 0px;
	background: #fff;
}
td.account_view {
	font-weight: bold;
	color: royalblue;
	font-size: smaller;	
	width: 25%;
	text-align: left;
}
td.resources {
	font-weight: bold;
	color: brown;
	font-size: larger;	
	width: 25%;
	text-align: left;
}
td.nav_menu{
	font-size: smaller;	
	font-weight: bold;
	color: brown;
	text-align: left;
}
td.current_stage {
	font-weight: bold;
	color: fuchsia;
	font-size: smaller;	
}
h3 {
	color: navy;
	font-size: larger;	
}
.hidden {display: none;}
.shown {display: inline;}
</style>
</head>
<!-- END menu -->
<!-- BEGIN admin_without_project -->
<body class="section-{section_num}">
<table class="head">
	<tr>
		<td align="left" valign="middle">

		<a href="{uri_base}/home.php">
			<img src="{uri_base}/images/s3db.png" border ="0" height="80" alt="S3DB">
		</a>
</td>
		<td align="right" valign="bottom">Login: <b>{login_user}</b><br/>{current_time}</td>
	</tr>
</table>
<ul id="menu">
  <li id="nav-1"><a href="{uri_base}/home.php">Home</a></li>
  <li id="nav-2"><a href="{uri_base}/admin/index.php">Admin</a>
    <ul id="subnav-2">
      <li><a href="{uri_base}/admin/user.php">User Manager</a></li>
      <li><a href="{uri_base}/admin/group.php">Group Manager</a></li>
      <li><a href="{uri_base}/admin/accesslog.php">View Access Log</a></li>
     
     
    </ul>
  </li>
  <li id="nav-3"><a href="{uri_base}/main.php">Project</a>
    
  </li>

  <li id="nav-4"><a href="{uri_base}/logout.php" target="_parent">Logout</a>
  </li>
</ul>
<!-- END admin_without_project -->
<!-- BEGIN admin -->
<body class="section-{section_num}">
<table class="head">
	<tr>
		<td align="left" valign="middle">
		<img src="{uri_base}/images/s3db.png" border ="0" height="80" alt="S3DB" onClick="window.open('http://www.s3db.org')">
		</td>
		<td align="right" valign="bottom">Login: <b>{login_user}</b><br/>{current_time}</td>
	</tr>
</table>
<ul id="menu">
  <li id="nav-1"><a href="{uri_base}/home.php">Home</a></li>
  <li id="nav-2"><a href="{uri_base}/admin/index.php">Admin</a>
    <ul id="subnav-2">
      <li><a href="{uri_base}/admin/user.php">User Manager</a></li>
      <li><a href="{uri_base}/admin/group.php">Group Manager</a></li>
      <li><a href="{uri_base}/admin/accesslog.php">View Access Log</a></li>
      <li><a href="{uri_base}/access_keys.php"  target="_parent">Access Keys</a></li>

    </ul>
  </li>
  <li id="nav-3"><a href="{uri_base}/main.php">Project</a>
    
  </li>
  <li id="nav-4"><a href="{uri_base}/logout.php" target="_parent">Logout</a>

  </li>
</ul>
<!-- END admin -->

<!-- BEGIN user -->
<body class="section-{section_num}">
<table class="head">
	<tr>
		<td>

		<a href="{uri_base}/home.php">
			<img src="{uri_base}/images/s3db.png" border ="0" height="80" alt="S3DB">
		</a>
</td>
		<td align="right" valign="bottom">Login: <b>{login_user}</b><br/>{current_time}</td>
	</tr>
</table>
<ul id="menu">

  <li id="nav-3"><a href="{uri_base}/main.php">Project</a>
    
  </li>
  <li id="nav-2"><a href="{uri_base}/changeprofile.php" target="_parent">My Account</a>
  <ul id="subnav-2">
      <li><a href="{uri_base}/access_keys.php" target="_parent">Access Keys</a></li>
  </ul>
  
  </li>
  <li id="nav-4"><a href="{uri_base}/logout.php" target="_parent">Logout</a>

  </li>
</ul>
<!-- END user -->
