<style type="text/css">
/*
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Title      : CityLights
Version    : 1.0
Released   : 20081119
Description: A Web 2.0 design with fluid width suitable for blogs and small websites.
Adapted for YuppCMS by: Pablo Pazos <pablo.swp@gmail.com>
*/

div.zone {
  <?php if ($mode=='edit') : ?>
    padding: 15px; /* Algo de pad para ver la zona */
    border: 1px dashed #cfcfcf;
  <?php endif; ?>
}

body {
	/* margin: 65px 0 0 0; */
    margin 0;
	padding: 0;
	background: #FFFFFF url(../../apps/cms2/skins/citylights/images/img01.jpg) repeat-x left top;
	text-align: justify;
	font: 15px Arial, Helvetica, sans-serif;
	color: #666666;
}

form {
	margin: 0;
	padding: 0;
}

input {
	padding: 5px;
	background: #FEFEFE;
	border: 1px solid #626262;
	font: normal 1em Arial, Helvetica, sans-serif;
}

h1, h1 a, h2, h2 a, h3, h3 a {
	margin: 0;
	text-decoration: none;
	font-family: Tahoma, Georgia, "Times New Roman", Times, serif;
	font-weight: normal;
	color: #3CB7FF;
}

h1 {
	letter-spacing: -1px;
	font-size: 2.2em;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

h2 {
	letter-spacing: -1px;
	font-size: 2em;
}

h3 {
	font-size: 1em;
}

p, ol, ul {
	margin-bottom: 2em;
	line-height: 200%;
}

blockquote {
	margin: 0 0 0 1.5em;
	padding-left: 1em;
	border-left: 5px solid #DDDDDD;
}

a {
	color: #3CB7FF;
}

a:hover {
	text-decoration: none;
}

/* Header */
#top_container { /* Contiene al top y al top_right*/
    padding: 0px 12% 0px 12%;
    display: table;
    height: 110px;
    width: 100%;
    margin: 14px 0 0 0;
}

#top {
    width: 50%;
    display: table-cell;
    padding: 0px;
    margin: 0px;
    vertical-align: middle;
}
#top h1, #top p {
	float: left;
	text-transform: lowercase;
}
#top h1 {
	font-size: 2.5em;
	padding: 0px 0 0 0px;
}
#top p {
	margin: 0;
	padding: 23px 0 0 4px;
	line-height: normal;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
#top a {
	text-decoration: none;
	color: #222426;
}
#top p a {
	color: #222426;
}


#top_right {
    width: 50%;
    display: table-cell;
    padding: 0px;
    margin: 0px;
    text-align: right;
    vertical-align: bottom;
}
#top_right ul {
	margin: 0;
	padding: 0;
	list-style: none;
}
#top_right li {
	display: block;
	float: left;
}
#top_right a {
	display: block;
	padding: 3px 20px 3px 20px;
	background: #3CB7FF;
	/*margin-top: 10px;*/
	margin-right: 2px;
	text-decoration: none;
	text-align: center;
	text-transform: capitalize;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
	color: #FFFFFF;
}
#top_right .last {
	margin-right: 0;
	padding-right: 0;
}
#top_right a:hover {
	background: #1B2025;
	color: #FFFFFF;
}
#top_right .current_page_item {
}
#top_right .current_page_item a {
	background: #1B2025;
	color: #FFFFFF;
}


#banner_container {
    margin: 13px 0 0 0;
    width: 100%;
}
#banner {
	background: url(../../apps/cms2/skins/citylights/images/img03.gif) no-repeat left top;
	height: 250px;
    margin-left: 10%;
    margin-right: 10%;
}

/* Page */

#page {
	margin: 15px 10% 0 10%;
}

/* Content */
#content_container {
    width: 100%;
    display: table;
}
#content {
	width: 75%;
	padding: 0 0px 0 0px;
    display: table-cell;
}

.post {
	margin-bottom: 10px;
}
.post .title {
	font-family: Tahoma, Georgia, "Times New Roman", Times, serif;
}
.post .title h2 {
	padding: 0px 30px 5px 0px;
	text-transform: lowercase;
	font-weight: normal;
	font-size: 2.2em;
	color: #3CB7FF;
}

.title h2 a {
	color: #3CB7FF;
}

.post .title p {
	margin: 0;
	padding: 10px 0 10px 20px;
	background: url(../../apps/cms2/skins/citylights/images/img02.gif) no-repeat left center;
	border-top: 4px #D8DFE6 solid;
	line-height: normal;
	color: #3CB7FF;
}
.post .title p a {
	color: #3CB7FF;
}
.post .entry {
	padding: 0;
}
.post .links {
	padding: 5px 0;
	border-bottom: 1px #D8DFE6 dotted;
	margin-top: 10px;
	text-align: left;
}
.post .links a {
	font-weight: bold;
}
.post .links a:hover {
}
.post .links .more {
	padding: 0 0 0 20px;
	background: url(../../apps/cms2/skins/citylights/images/img04.gif) no-repeat left 50%;
}
.post .links .comments {
	margin-left: 20px;
	background: url(../../apps/cms2/skins/citylights/images/img05.gif) no-repeat left 50%;
	padding: 0 0 0 20px;
}

/* Sidebar */

#right {
	display: table-cell;
	width: 22%;
    
}

#right ul {
	margin: 0;
	padding: 0;
	list-style: none;
}

#right li {
	margin-bottom: 10px;
}

#right li ul {
	padding: 0 0px 40px 30px;
}

#right li li {
	margin: 0;
	padding-left: 30px;
	background: url(../../apps/cms2/skins/citylights/images/img02.gif) no-repeat 5px 50%;
}

#right h2 {
	padding: 0px 30px 10px 30px;
	text-transform: lowercase;
	font-weight: normal;
	font-size: 1.6em;
	color: #3CB7FF;
}


/* Search */

#search {
	padding: 20px 30px 40px 30px;
}

#search input {
	padding: 0;
	width: 70px;
	height: 29px;
	font-weight: bold;
}

#search #s {
	padding: 5px;
	width: 150px;
	height: auto;
	border: 1px solid #626262;
	font: normal 1em Arial, Helvetica, sans-serif;
}

#search br {
	display: none;
}

/* Categories */

#sidebar #categories li {
}

/* Calendar */

#calendar_wrap {
	padding: 0 30px 40px 30px;
}

#calendar table {
	width: 100%;
	text-align: center;
}

#calendar thead {
	background: #F1F1F1;
}

#calendar tbody td {
	border: 1px solid #F1F1F1;
}

#calendar #prev {
	text-align: left;
}

#calendar #next {
	text-align: right;
}

#calendar tfoot a {
	text-decoration: none;
	font-weight: bold;
}

#calendar #today {
	background: #FFF3A7;
	border: 1px solid #EB1400;
	font-weight: bold;
	color: #EB1400
}

/* Footer */

#footer_container {
    border-top: 2px #EEEEEE solid;
    background: #F5F5F5;
}

#footer {
	padding: 20px 0 20px 0;
    margin: 0 10% 0 10%;
}

#footer p {
	margin-bottom: 1em;
	text-align: center;
	line-height: normal;
	font-size: .9em;
	background: #F5F5F5;
}

#footer a {
	padding: 0 20px;
	text-decoration: none;
	color: #187BD9;
}

#footer a:hover {
}

#footer .rss {
}

#footer .xhtml {
}

#footer .css {
}

#footer .legal a {
	padding: 0;
}

#login_box {
    border-width: 0 3px 3px 3px;
    border-style: solid;
    border-color: #3CB7FF;
    margin-top: 16px;
}

.bar {
    margin: 5px 10% 5px 10%;
    padding: 3px;
}

.moduleContainer {
  padding: 10px;
}
</style>