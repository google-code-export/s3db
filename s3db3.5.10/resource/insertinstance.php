<?php
#insertinstance.php is a form for adding instances
	#Includes links to instance page, as well as import from excel
	#Helena F Deus (helenadeus@gmail.com), June 28 2007
 ini_set('display_errors',0);
	if($_REQUEST['su3d'])
	ini_set('display_errors',1);
include('instanceheader.php');
#relevant extra arguments
#$args = '?key='.$_REQUEST['key'].'&project_id='.$_REQUEST['project_id'].'&class_id='.$_REQUEST['class_id'];


#define actions for the page
#include('../webActions.php');
#echo '<pre>';print_r($resource_info);
$class_id = ($_REQUEST['collection_id']!='')?$_REQUEST['collection_id']:$_REQUEST['class_id'];
$resource_info = URIinfo('C'.$class_id, $user_id, $key, $db);


if($class_id=='' && $resource_info=='')
{
echo "Please specify a valid collection_id";
exit;
}
else
{

if(!$resource_info['add_data'])
	{	
	echo "User cannnot add items in this collection";
	exit;
	
	}
else
{

	#form actions - have to com ebefore the rest of the scirpt bacuse of the headers...
	if($_POST['add_resource'])
		{
				$s3ql=compact('user_id','db');
				$s3ql['insert'] = 'item';
				$s3ql['where']['collection_id'] = $class_id;
				
							
				if($_POST['notes']!='')
				{$s3ql['where']['notes'] = nl2br($_POST['notes']);}	
				
				$s3ql['format']='html';
				$done = S3QLaction($s3ql);
				$msg = html2cell($done);
				#echo $done;
				
				
				
				#ereg('<item_id>([0-9]+)</item_id>', $done, $s3qlout);
				
				$instance_id = $msg[2]['item_id'];
					#preg_match('/[0-9]+/', $done, $instance_id);
				$item_id = $instance_id;
				
				if($instance_id!='')
					{
						#now add the users
					$element = 'item';

					$message .= addUsers(compact('users','user_id', 'db', 'class_id', 'collection_id','element','item_id'));
					
					Header('Location: '.$action['instanceform'].'&item_id='.$instance_id);
						exit;
					}
				else
					echo $msg[2]['message'];
					
		
		
		}

	#include all the javascript functions for the menus...
	include('../S3DBjavascript.php');

	#and the short menu for the resource script
	include('../action.header.php');

	#add the form for inserting instances
	

}
	
}//closes no permission
$new=1;
$aclGrid = aclGrid(compact('user_id', 'db', 'users','new'));
?>
<table class="create_resource" width="70%" border="0">
	<tr><td class="message" colspan="9"></td></tr>
	<tr><td></tr></td>
	<tr bgcolor="#FF9900"><td colspan="9" align="center">
		
		<?php
		echo "Add Several <b>".$resource_info['entity']."</b> at a time</td></tr>";
		?>
			<tr>
				<td colspan="12">
				<center><br>
				<input type="hidden" name="entity" value="Dogs">
				<?php
				echo '<input type="button" name="takemetoupload" value="Import '.$resource_info['entity'].' from File" onclick="window.location=\''.$action['excelimport'].'\'"><br />Note: Only tab separated files are valid';
				?>
				<br><br>
				</center>
				</td>
			</tr>
				
			
			<?php
			echo '<form name="insertAcl" method="POST" action="'.$action['insertinstance'].'" autocomplete="on">';
			
			echo '<tr bgcolor="#FF9900"><td colspan="9" align="center">Add One <b>'.$resource_info['entity'].'</b> at a time</td></tr>';
			?>
			<tr class="odd" align="center">
				<td width="10%">Owner</td>
				<td width="10%">Resource<sup class="required"></sup></td>
				<td width="20%">Notes</td>
				<td width="10%">Action</td>
			</tr>
	

			<tr valign="top" align="center">
				<?php
				echo '<td width="10%">'.find_user_loginID(array('db'=>$db, 'account_id'=>$user_id)).'</td>';
				echo '<td width="15%">'.$resource_info['entity'].'</td>';
				?>
				<td width="30%"><textarea name="notes" style="background: lightyellow" rows="2" cols="40"></textarea></td>
				<td width="10%" align="center">
				
				<?php
				echo '<input type="submit" name="add_resource" value="Add '.$resource_info['entity'].'"></td>';
				?>
				<tr><td colspan="9" align="center"><BR><BR></td></tr>
				<tr bgcolor="#FF9900"><td colspan="9" align="center">Users</td></tr>
				
				<?php echo $aclGrid; ?>
			</tr>
	</form>
</table>