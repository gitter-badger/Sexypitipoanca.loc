<?php
class TS_Message
{
	public static function getUserRequestsNumber($receiverUserId)
	{
		$return = NULL;
		$model = new Default_Model_SocialUserConnections();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('social_user_connections'), array('id'=>'COUNT(id)'))
				->where('receiverUserId = ?', $receiverUserId)
				->where('isConfirmed IS FALSE');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;
	}
	
	public static function sendMessage($to, $from, $content)
	{
		$return = FALSE;
		$messageModel = new Default_Model_Message();
		$messageModel->setTo($to);
		$messageModel->setFrom($from);
		$messageModel->setContent($content);
		if($messageModel->save())
		{
			$return = TRUE;
		}
		return $return;
	}

	
	public static function getNotificationsNumber($userId)
	{
		$return = 0;
		$messageModel = new Default_Model_Notifications();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->from(array('nu'=>'notifications'), array('id'=>'COUNT(nu.id)'))
				->joinLeft(array('u'=>'notification_users'),'nu.id = u.notificationId',array('ucreated'=>'u.created'))
				->where('u.sentTo = ?',$userId)				
				->where('u.read = ?', '0')
				->setIntegrityCheck(false);		
		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			$return = $messages[0]->getId();
		}
		return $return;
	}
	
	public static function getMessageNumber($sentTo)
	{
		$return = 0;
		$messageModel = new Default_Model_Message();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->from(array('messages'), array('id' => 'COUNT(id)'))
				->where('`sentTo` = ?', $sentTo)
				->where('`read` != ?',1)
//				->group('from')
				->order('created DESC');		
		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			$return = $messages[0]->getId();
		}
		return $return;
	}
	
	public static function getUserMessages($sentTo)
	{
		$messageModel = new Default_Model_Message();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where('sentTo = ? ', $sentTo)
				->group('from')
				->order('created DESC');
		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			return $messages;
		}
		else
		{
			return NULL;
		}
	}
	
	public static function getMessageNotificationsFromUser($sentTo)
	{
		$result = array();
		$messageModel = new Default_Model_Message();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where('sentTo = ? ', $sentTo)
				->group('from')
				->order('created DESC');
		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			foreach ($messages as $key => $value) 
			{
				$result[] = self::getLastMessage($sentTo,$value->getFrom());
			}
			
			return $result;
		}
		else
		{
			return NULL;
		}
	}
	
	public static function getLastMessage($sentTo, $from)
	{
		$messageModel = new Default_Model_Message();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where('`sentTo` = ? ', $sentTo)
				->where('`from` = ? ', $from)
				->order('created DESC')
				->limit(1);

		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			return $messages[0];
		}
		else
		{
			return NULL;
		}
	}
	
	public static function getMessagesFromUser($sentTo, $from)
	{
		$messageModel = new Default_Model_Message();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where("`sentTo` = {$sentTo} AND `from` = {$from}")
				->orwhere("`sentTo` = {$from} AND `from` = {$sentTo}")
				->order('created ASC');
		$messages = $messageModel->fetchAll($select);
		if(NULL != $messages)
		{
			return $messages;
		}
		else
		{
			return NULL;
		}
	}
	
	public static function markMessagesRead($messageId)
	{
		// BEGIN: Mark all massages as read		
		$messageModel = new Default_Model_Message();
		if($messageModel->find($messageId))
		{
			$messageModel->setRead(1);
			$messageModel->save();
		}
		
		// END: Mark all massages as read
	}
	
	public static function markNotificationRead($messageId,$userId)
	{
		// BEGIN: Mark all massages as read		
		$messageModel = new Default_Model_NotificationUsers;
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where('sentTo = ?',$userId)
				->where('notificationId = ?',$messageId);
		if(($result = $messageModel->fetchAll($select))) 
		{
			foreach ($result as $value){
				$messModel = new Default_Model_NotificationUsers;
				$messModel->find($value->getId());				
				$messModel->setRead(1);
				$messModel->save();
			}
			
		}
		
		// END: Mark all massages as read
	}
	
	public static function getUsersByNotificationId($notificationId)
	{
		$messageModel = new Default_Model_AccountUsers();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->from(array('u'=>'j_account_users'))
				->join(array('nu'=>'notification_users'),'u.id = nu.sentTo',array('ncreated'=>'nu.created'))
				->where('nu.notificationId = ?',$notificationId)
				->order('created ASC')
				->setIntegrityCheck(false);
		$result = $messageModel->fetchAll($select);
		$users = array();
		if(null != $result)
		{
			foreach ($result as $value) {
				$users[] = $value->getUsername();
			}
		}
		$allUsernames = '';
		if($users){
			$allUsernames = implode(', ', $users);
		}
		return $allUsernames;
	}
}