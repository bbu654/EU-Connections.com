<!--------------------------------------------------------------------->
<!-- Main Table, Row #2 =                                         ----->
<!--    Column 1 = Left-side Menu (spans 2 rows)                  ----->
<!--    Column 2 = Gutter (spans 2 rows)                          ----->
<!--    Column 3 = Right-side Page Content (one row only)         ----->
<!--------------------------------------------------------------------->
<tr>
   <!------------------------------------------------------------------>
   <!--TD = Left-side menu (spans 2 rows).                        ----->
   <!------------------------------------------------------------------>
   <td rowspan="2" width="134" align="left" valign="top" bgcolor="#FFCC00">
   <font color="#333399">
      <font size="-1">
      <!--- PHP code to display current date ------------------->
	  <? echo date ("F j, Y " ) ; ?>
      </font>

      <p></p>

       <!--------------------------------------------------------->
       <!-- Left-side Menu:                                   ---->
       <!--    Section headings = font size "+1",             ---->
       <!--    Page links =       font size "-1",             ---->
       <!--    Current page =     uppercase bold, no link     ---->
       <!--------------------------------------------------------->

       <font size="+1"><b>Main Menu</b></font><br />
       <font size="-1"> 
        &nbsp; &nbsp; <a href="member.php?page=home">Console Home</a><br />
        &nbsp; &nbsp; <a href="index.php?page=home">Website Home</a><br />
        &nbsp; &nbsp; <a href="logoff.php">Logout</a><br />
        <br />
        </font>

<?php  if( $user_admin >= 2 ){  ?>
      <font size="+1"><b>Admin Menu</b></font><br />
       <font size="-1"> 
        &nbsp; &nbsp; <a href="member_roster.php">Member Spreadsheet</a><br /> 
        &nbsp; &nbsp; <a href="member.php?page=email">Email Members</a><br />
        &nbsp; &nbsp; <a href="member.php?page=member">Add New Member</a><br />
        &nbsp; &nbsp; <a href="member.php?page=maint_roster">Member Maintenance</a><br />
        &nbsp; &nbsp; <a href="member.php?page=password_reset">Reset Passwords</a><br />
        &nbsp; &nbsp; <a href="member.php?page=maint_roster&select_val=inactive">Update Alumni</a><br />
        &nbsp; &nbsp; <a href="member.php?page=review_profiles">Release Profiles</a><br />
        &nbsp; &nbsp; <a href="member.php?page=add_link">Add Useful Link</a><br />
        &nbsp; &nbsp; <a href="member.php?page=maint_links">Edit Links</a><br />
        <br />
        </font>
<?php }?>       
        <font size="+1"><b>Member Menu</b></font><br />
        <font size="-1"> 
        &nbsp; &nbsp; <a href="member.php?page=info">View My Info</a><br />
        &nbsp; &nbsp; <a href="member.php?page=update_contact&euid=<?= $user_euid ?>">Update Contact Info</a><br />
        &nbsp; &nbsp; <a href="member.php?page=update_profiles">Update Profiles</a><br />
        &nbsp; &nbsp; <a href="member.php?page=update_pass">Change Password</a><br />
        &nbsp; &nbsp; <a href="member.php?page=library">Library</a><br>
        &nbsp; &nbsp; <a href="member.php?page=roster">View Roster</a><br />
        <br />
        </font>
<?php  if( $user_admin >= 2 ){  ?>
      <font size="+1"><b>WebIntellects Menu</b></font><font size="-1"><br />(webmaster only)</font><br />
       <font size="-1"> 
        &nbsp; &nbsp; <a href="http://webmail.eu-connections.org" target="_blank">Site Webmail</a><br /> 
        &nbsp; &nbsp; <a href="http://cm.controlmaestro.com" target="_blank">Account Management</a><br />
        &nbsp; &nbsp; <a href="https://www.eu-connections.org:8443" target="_blank">Site Administrator</a><br />
        &nbsp; &nbsp; <a href="/user" target="_blank">User Administrator</a><br />
        </font>
<?php }?>   
   </font>
   </td>

  <!------------------------------------------------------------------>
  <!-- TD = Table gutter #1  (spans 2 rows).                      ---->
  <!------------------------------------------------------------------>
  <td rowspan= "2" width="3">&nbsp;  </td>    